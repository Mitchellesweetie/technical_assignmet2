<?php

namespace App\Contracts;

use App\Models\StockUpload;

interface TopPerformersAnalyzerInterface
{
    public function analyze(StockUpload $upload, int $limit = 5): array;
}