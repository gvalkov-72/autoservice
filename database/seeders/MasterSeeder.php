<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            CustomerImportSeeder::class,
            //CustomerSeeder::class,
            //VehicleImportSeeder::class,
            //VehicleSeeder::class,
            ProductImportSeeder::class,
            //ProductSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}