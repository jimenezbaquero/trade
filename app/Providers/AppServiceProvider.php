<?php

namespace App\Providers;

use App\Domain\Strategies\StrategyRegistry;
use App\Observers\CandleObserver;
use App\Services\StrategyService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StrategyService::class, function ($app) {
            return new StrategyService(
                $app->make(StrategyRegistry::class)->get()
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
