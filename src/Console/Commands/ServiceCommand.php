<?php

namespace Core\Modules\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:configure';

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
        File::copy(dirname(__DIR__, 2) . '/database/migrations/create_modules_table.php', database_path('migrations/create_modules_table.php'));
        Artisan::call('migrate');
    }
}






