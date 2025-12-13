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
            CustomerSeeder::class,
            VehicleSeeder::class,
            ProductSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}