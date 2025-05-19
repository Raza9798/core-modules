<?php

namespace Core\Modules;

use Illuminate\Support\ServiceProvider;
use Core\Modules\Console\Commands\GenerateResourceCommand;
use Illuminate\Filesystem\Filesystem;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $paths = glob(base_path('database/migrations/*'), GLOB_ONLYDIR);
        $this->loadMigrationsFrom($paths);
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
