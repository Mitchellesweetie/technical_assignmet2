<?php

namespace Tests\Unit;

use App\Models\StockPrice;
use App\Models\StockUpload;
use App\Models\User;
use App\Services\TopPerformersAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopPerformersAnalyzerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_ranks_stocks_by_max_price_gain(): void
    {
        $user = User::factory()->create();

        $upload = StockUpload::create([
            'user_id' => $user->id,
            'filename' => 'test.csv',
        ]);

        StockPrice::create([
            'stock_upload_id' => $upload->id,
            'stock_name' => 'Stock A',
            'price' => 10,
            'date' => '2019-01-01',
        ]);
        StockPrice::create([
            'stock_upload_id' => $upload->id,
            'stock_name' => 'Stock A',
            'price' => 20,
            'date' => '2019-01-02',
        ]);

        StockPrice::create([
            'stock_upload_id' => $upload->id,
            'stock_name' => 'Stock B',
            'price' => 100,
            'date' => '2019-01-01',
        ]);
        StockPrice::create([
            'stock_upload_id' => $upload->id,
            'stock_name' => 'Stock B',
            'price' => 150,
            'date' => '2019-01-02',
        ]);

        $analyzer = new TopPerformersAnalyzer();
        $result = $analyzer->analyze($upload, 5);

        $this->assertSame('Stock B', $result['performers']->first()['stock']);
        $this->assertSame(50.0, $result['performers']->first()['gain']);
        $this->assertCount(2, $result['performers']);
        $this->assertCount(2, $result['chart']['labels']);
    }

    public function test_it_returns_only_top_five(): void
    {
        $user = User::factory()->create();
        $upload = StockUpload::create([
            'user_id' => $user->id,
            'filename' => 'test.csv',
        ]);

        foreach (range(1, 7) as $i) {
            StockPrice::create([
                'stock_upload_id' => $upload->id,
                'stock_name' => "Stock {$i}",
                'price' => 10,
                'date' => '2019-01-01',
            ]);
            StockPrice::create([
                'stock_upload_id' => $upload->id,
                'stock_name' => "Stock {$i}",
                'price' => 10 + $i,
                'date' => '2019-01-02',
            ]);
        }

        $result = (new TopPerformersAnalyzer())->analyze($upload, 5);

        $this->assertCount(5, $result['performers']);
    }
}