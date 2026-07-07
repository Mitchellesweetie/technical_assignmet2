<?php

namespace Tests\Unit;

use App\Services\StockFileParser;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Tests\TestCase;

class StockFileParserTest extends TestCase
{
    public function test_it_parses_valid_csv_rows(): void
    {
        $csv = <<<'CSV'
stock,price,date
Eaagads Ltd,14.5,2019-01-02
Limuru Tea,500,2019-01-03
CSV;

        $file = UploadedFile::fake()->createWithContent('stocks.csv', $csv);

        $records = (new StockFileParser())->parse($file);

        $this->assertCount(2, $records);
        $this->assertSame('Eaagads Ltd', $records[0]['stock']);
        $this->assertSame(14.5, $records[0]['price']);
        $this->assertSame('2019-01-02', $records[0]['date']);
    }

    public function test_it_rejects_invalid_price(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $csv = <<<'CSV'
stock,price,date
Bad Stock,not-a-number,2019-01-02
CSV;

        $file = UploadedFile::fake()->createWithContent('stocks.csv', $csv);

        (new StockFileParser())->parse($file);
    }
}
