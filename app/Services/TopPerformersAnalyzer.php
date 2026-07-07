<?php

namespace App\Services;

use App\Models\StockUpload;
use Illuminate\Support\Collection;
use App\Contracts\TopPerformersAnalyzerInterface;

class TopPerformersAnalyzer implements TopPerformersAnalyzerInterface
{
    /**
     * Analyze the stock upload and return the top performers.
     *
     * @param  StockUpload  $upload
     * @param  int  $limit
     * @return array
     */
    public function analyze(StockUpload $upload, int $limit = 5): array
    {
        $prices = $upload->stockPrices()
            ->orderBy('date')
            ->get()
            ->groupBy('stock_name');

        $performers = $prices->map(function (Collection $entries, string $stockName) {
            $sorted = $entries->sortBy('date')->values();
            $runningMin = null;
            $maxGain = 0.0;
            $startPrice = (float) $sorted->first()->price;
            $peakPrice = $startPrice;

            foreach ($sorted as $entry) {
                $price = (float) $entry->price;

                if ($runningMin === null || $price < $runningMin) {
                    $runningMin = $price;
                }

                $gain = $price - $runningMin;

                if ($gain > $maxGain) {
                    $maxGain = $gain;
                    $peakPrice = $price;
                }
            }

            $basePrice = $runningMin ?? $startPrice;

            return [
                'stock' => $stockName,
                'gain' => round($maxGain, 4),
                'gain_percent' => $basePrice > 0 ? round(($maxGain / $basePrice) * 100, 2) : 0.0,
                'start_price' => round($basePrice, 4),
                'peak_price' => round($peakPrice, 4),
            ];
        })
            ->sortByDesc('gain')
            ->values()
            ->take($limit);

        $allDates = $prices->flatten(1)
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString())
            ->unique()
            ->sort()
            ->values();

        $colors = [
            '#2563eb',
            '#16a34a',
            '#ea580c',
            '#9333ea',
            '#dc2626',
        ];

        $barColors = $performers->values()->map(
            fn ($_, int $index) => $colors[$index % count($colors)]
        )->all();

        return [
            'performers' => $performers,
            'chart' => [
                'labels' => $performers->pluck('stock')->all(),
                'datasets' => [
                    [
                        'label' => 'Max price gain',
                        'data' => $performers->pluck('gain')->all(),
                        'backgroundColor' => $barColors,
                        'borderColor' => $barColors,
                        'borderWidth' => 1,
                        'borderRadius' => 8,
                    ],
                ],
            ],
            'period' => [
                'start' => $allDates->first(),
                'end' => $allDates->last(),
            ],
        ];
    }
}