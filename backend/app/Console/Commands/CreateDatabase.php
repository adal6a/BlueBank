<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea el arhivo de base de datos bluebank.sqlite';

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
     * @return mixed
     */
    public function handle()
    {
        touch(database_path() .'/bluebank.sqlite');

        return 0;
    }
}
