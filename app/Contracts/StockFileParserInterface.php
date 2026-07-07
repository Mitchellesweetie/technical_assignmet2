<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface StockFileParserInterface
{
    /**
     * @return array<int, array{stock: string, price: float, date: string}>
     */
    public function parse(UploadedFile $file): array;
}