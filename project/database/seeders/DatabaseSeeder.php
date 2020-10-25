<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Execute seeder.
     */
    public function run()
    {
        $this->call(BankAccountSeeder::class);
    }
}
