<?php

namespace Laravel\Modules\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateResourceCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make {module : The name of the module} {name : The name of the resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         * Directories should be
         * - app/Http/Controllers/{module}/{name}Controller.php
         * - app/Models/{module}/{name}.php
         * - app/Policies/{module}/{name}Policy.php
         * - database/factories/{module}/{name}Factory.php
         * - database/migrations/{module}/create_{name}_table.php
         * - database/seeds/{module}/{name}Seeder.php
         * - resources/views/{module}/{name}.blade.php
         */
        $module  = $this->argument('module');
        $name = $this->argument('name');

        $anticipate = $this->choice(
            'What do you want to generate?',
            [
                'all resources (controller, model, policy, factory, migration, seeder, view)',
                'controller',
                'model',
                'policy',
                'factory',
                'migration',
                'seeder',
                'view'
            ],
        );
        if ($this->confirm('Do you wish to continue?')) {
            switch ($anticipate) {
                case 'all resources (controller, model, policy, factory, migration, seeder, view)':
                    $this->allResources($module, $name);
                    break;
                case 'controller':
                    $this->controller($module, $name);
                    break;
                case 'model':
                    $this->model($module, $name);
                    break;
                case 'policy':
                    $this->policy($module, $name);
                    break;
                case 'factory':
                    $this->factory($module, $name);
                    break;
                case 'migration':
                    $this->migration($module, $name);
                    break;
                case 'seeder':
                    $this->seeder($module, $name);
                    break;
                case 'view':
                    $this->view($module, $name);
                    break;
            }

            $this->configureModule($module);
            $this->info('Resource generation completed.');
        } else {
            $this->warn('Resource generation discarded.');
        }

        $this->info('RUN: php artisan module:make to generate resources.');
    }

    private function configureModule($module)
    {
        $module = Str::ucfirst(Str::lower($module));
        $slug = Str::slug($module);

        $existingModule = DB::table('modules')->where('slug', $slug)->first();
        if (!$existingModule) {
            DB::table('modules')->insert([
                'name' => $module,
                'slug' => $slug,
                'description' => 'This is a '. $module. ' module',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    

    private function makeDirectory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    private function allResources($module, $name)
    {
        $this->controller($module, $name);
        $this->model($module, $name);
        $this->policy($module, $name);
        $this->factory($module, $name);
        $this->migration($module, $name);
        $this->seeder($module, $name);
        $this->view($module, $name);
    }

    private function controller($module, $name)
    {
        $module = Str::ucfirst(Str::lower($module));
        $name = Str::ucfirst(Str::lower($name));
        $this->makeDirectory(app_path("Http/Controllers/{$module}"));
        Artisan::call('make:controller', [
            'name' => "{$module}\\{$name}Controller",
            '--resource' => true
        ]);
    }

    private function model($module, $name)
    {
        $module = Str::ucfirst(Str::lower($module));
        $name = Str::ucfirst(Str::lower($name));
        $this->makeDirectory(app_path("Models/{$module}"));
        Artisan::call('make:model', [
            'name' => "{$module}\\{$name}"
        ]);
    }

    private function policy($module, $name)
    {
        $module = Str::ucfirst(Str::lower($module));
        $name = Str::ucfirst(Str::lower($name));
        $this->makeDirectory(app_path("Policies/{$module}"));
        Artisan::call('make:policy', [
            'name' => "{$module}\\{$name}Policy"
        ]);
    }

    private function factory($module, $name)
    {
        $module = Str::ucfirst(Str::lower($module));
        $name = Str::ucfirst(Str::lower($name));
        $this->makeDirectory(database_path("factories/{$module}"));
        Artisan::call('make:factory', [
            'name' => "{$module}\\{$name}Factory"
        ]);
    }

    private function migration($module, $name)
    {
        $module = Str::ucfirst(Str::lower($module));
        $name = Str::lower($name);

        if (Schema::hasTable($name)) {
            $this->warn("Table '{$name}' already exists and not created.");
            return;
        }

        $this->makeDirectory(database_path("migrations/{$module}"));
        Artisan::call('make:migration', [
            'name' => "create_{$name}_table",
            '--path' => "database/migrations/{$module}"
        ]);
    }

    private function seeder($module, $name)
    {
        $module = Str::ucfirst(Str::lower($module));
        $name = Str::ucfirst(Str::lower($name));
        $this->makeDirectory(database_path("seeders/{$module}"));
        Artisan::call('make:seeder', [
            'name' => "{$module}\\{$name}Seeder"
        ]);
    }

    private function view($module, $name)
    {
        $module = Str::ucfirst(Str::lower($module));
        $name = Str::ucfirst(Str::lower($name));
        $this->makeDirectory(resource_path("views/{$module}"));
        Artisan::call('make:view', [
            'name' => "{$module}.{$name}"
        ]);
    }
}
