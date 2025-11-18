<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\UnitDatabaseSeeder;

class SeedUnitDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-unit {connection} {companyId} {--name=} {--code=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed a unit database with company data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = $this->argument('connection');
        $companyId = $this->argument('companyId');
        $name = $this->option('name');
        $code = $this->option('code');

        $companyData = [];
        if ($name) {
            $companyData['name'] = $name;
        }
        if ($code) {
            $companyData['code'] = $code;
        }

        $seeder = new UnitDatabaseSeeder();
        $seeder->setCommand($this);
        $seeder->run($connection, $companyId, $companyData);

        return 0;
    }
}
