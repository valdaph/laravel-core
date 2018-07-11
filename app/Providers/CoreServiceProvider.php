<?php

namespace Valda\Providers;

use Illuminate\Support\ServiceProvider;
use Valda\Commands\ValdaScoutImportCommand;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            ValdaScoutImportCommand::class,
        ]);
        
        $this->loadViewsFrom(__DIR__ . '/../../views', 'valda');
    }
}
