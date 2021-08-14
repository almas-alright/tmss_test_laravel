<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TmssTestInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmss-test:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Installing...Tmss Test Assignemnt..');
        try {
            DB::connection()->getPdo();
            $this->info('...');
        } catch (\Exception $e) {
            $this->error('!!database not connected!!');
            exit();
        }
        $this->info('start');
        Artisan::call('jwt:secret');
        Artisan::call('migrate');
        Artisan::call('migrate', ['--path' => 'database/migrations/assignment_db']);
        Artisan::call('migrate', ['--path' => 'database/migrations/api']);
        Artisan::call('db:seed');
        $this->info('--ইনস্টল হয়ে গেছে--');

        $this->newLine();
        $this->table(['email', 'password'], [['kutsnalmas@gmail.com', 'abcd1234']]);

    }
}
