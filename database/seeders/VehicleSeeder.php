<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        Vehicle::create([
            'customer_id' => 1,
            'vin'         => 'WAUZZZ8V8FA123456',
            'plate'       => 'СВ1234АВ',
            'make'        => 'Audi',
            'model'       => 'A4',
            'year'        => 2018,
            'mileage'     => 125_000,
            'dk_no'       => 'DK-2024-001',
            'notes'       => 'Смяна на масло и филтри',
        ]);

        Vehicle::create([
            'customer_id' => 2,
            'vin'         => 'WBAZZZ32040A654321',
            'plate'       => 'СА5678СТ',
            'make'        => 'BMW',
            'model'       => '320d',
            'year'        => 2020,
            'mileage'     => 78_000,
            'dk_no'       => 'DK-2024-002',
            'notes'       => 'Проверка на спирачки',
        ]);
    }
}