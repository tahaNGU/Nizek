<?php

namespace App\Providers;

use App\Repositories\StockStream\StockStreamEloquentInterface;
use App\Repositories\StockStream\StockStreamEloquentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StockStreamEloquentInterface::class, StockStreamEloquentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
