<?php

namespace Core\Modules;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Core\Modules\Console\Commands\GenerateResourceCommand;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
       
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateResourceCommand::class,
            ]);
        }
    }
}
