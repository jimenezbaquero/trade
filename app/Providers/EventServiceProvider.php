<?php

namespace App\Providers;



use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// EVENTS
use App\Events\CandleChanged;
use App\Events\IndicatorsCalculated;
// LISTENERS
use App\Listeners\HandleCandleChanged;
use App\Listeners\HandleIndicatorsCalculated;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        
        CandleChanged::class => [HandleCandleChanged::class],
        IndicatorsCalculated::class => [HandleIndicatorsCalculated::class],

    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be auto-discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false; // importante para control manual
    }
}
