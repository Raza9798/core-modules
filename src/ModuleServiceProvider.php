<?php

namespace Laravel\Modules;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Laravel\Modules\Console\Commands\ServiceCommand;
use Laravel\Modules\Console\Commands\GenerateResourceCommand;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
       
        try {
            $modules = DB::table('modules')->where('is_active', true)->get() ?? [];
            foreach ($modules as $module) {
                $this->loadMigrationsFrom(database_path("migrations/{$module->name}"));
            }
        } catch (\Exception $e) {
        }
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ServiceCommand::class,
                GenerateResourceCommand::class,
            ]);
        }
    }
}
