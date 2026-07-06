<?php

namespace App\Services;

use App\Models\StockUpload;
use Illuminate\Support\Collection;

class TopPerformersAnalyzer
{
    /**
     * @return array{
     *     performers: Collection<int, array{stock: string, gain: float, gain_percent: float, start_price: float, peak_price: float}>,
     *     chart: array{labels: array<int, string>, datasets: array<int, array{label: string, data: array<int, float|null>, borderColor: string, backgroundColor: string, tension: float, fill: bool}>},
     *     period: array{start: string|null, end: string|null}
     * }
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

        $topStockNames = $performers->pluck('stock');
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

        $datasets = $performers->values()->map(function (array $performer, int $index) use ($prices, $allDates, $colors) {
            $stockPrices = $prices->get($performer['stock'], collect())
                ->keyBy(fn ($entry) => $entry->date->toDateString());

            return [
                'label' => $performer['stock'],
                'data' => $allDates->map(
                    fn (string $date) => isset($stockPrices[$date]) ? (float) $stockPrices[$date]->price : null
                )->all(),
                'borderColor' => $colors[$index % count($colors)],
                'backgroundColor' => $colors[$index % count($colors)],
                'tension' => 0.35,
                'fill' => false,
                'spanGaps' => true,
            ];
        })->all();

        return [
            'performers' => $performers,
            'chart' => [
                'labels' => $allDates->all(),
                'datasets' => $datasets,
            ],
            'period' => [
                'start' => $allDates->first(),
                'end' => $allDates->last(),
            ],
        ];
    }
}
