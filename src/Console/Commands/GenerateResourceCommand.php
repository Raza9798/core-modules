<?php

namespace Core\Modules\Console\Commands;

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
                'Api resources (controller, model, factory, migration, seeder)',
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
                case 'Api resources (controller, model, factory, migration, seeder)':
                    $this->controller($module, $name);
                    $this->model($module, $name);
                    $this->factory($module, $name);
                    $this->migration($module, $name);
                    $this->seeder($module, $name);
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

            $this->info('Resource generation completed.');
        } else {
            $this->warn('Resource generation discarded.');
        }

        $this->info('RUN: php artisan module:make to generate resources.');
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
        Artisan ::call("make:controller $module/{$name}Controller --resource");
    }

    private function model($module, $name)
    {
        Artisan ::call("make:model $module/$name");
    }

    private function policy($module, $name)
    {
        Artisan::call("make:policy $module/{$name}Policy");
    }

    private function factory($module, $name)
    {
        Artisan::call("make:factory $module/{$name}Factory");
    }

    private function migration($module, $name)
    {
        $module = Str::ucfirst(Str::lower($module));
        $name = Str::snake(Str::pluralStudly($name));

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
        Artisan::call("make:seeder $module/{$name}Seeder");
    }

    private function view($module, $name)
    {
        Artisan::call("make:view $module/{$name}");
    }
}
