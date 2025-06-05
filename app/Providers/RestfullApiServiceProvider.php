<?php

namespace App\Providers;

use App\RestFullApi\ApiResponseBuilder;
use Illuminate\Support\ServiceProvider;

class RestfullApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('ApiResponse',function(){
            return new ApiResponseBuilder;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}