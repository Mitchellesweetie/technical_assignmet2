<?php

namespace App\Providers;

use App\Contracts\StockFileParserInterface;
use App\Contracts\TopPerformersAnalyzerInterface;
use App\Services\StockFileParser;
use App\Services\TopPerformersAnalyzer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(StockFileParserInterface::class, StockFileParser::class);
        $this->app->bind(TopPerformersAnalyzerInterface::class, TopPerformersAnalyzer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
