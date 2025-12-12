<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Ремонт на двигател', 'sort_order' => 1],
            ['name' => 'Спирачна система', 'sort_order' => 2],
            ['name' => 'Окачване и управление', 'sort_order' => 3],
            ['name' => 'Електрическа система', 'sort_order' => 4],
            ['name' => 'Обслужване', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }

        $services = [
            ['code' => 'SERV-001', 'name' => 'Смяна на масло и филтър', 'price' => 30.00, 'category_id' => 5],
            ['code' => 'SERV-002', 'name' => 'Ремонт на спирачки', 'price' => 80.00, 'category_id' => 2],
            ['code' => 'SERV-003', 'name' => 'Смяна на акумулатор', 'price' => 25.00, 'category_id' => 4],
            ['code' => 'SERV-004', 'name' => 'Ремонт на окачване', 'price' => 120.00, 'category_id' => 3],
            ['code' => 'SERV-005', 'name' => 'Диагностика на двигател', 'price' => 50.00, 'category_id' => 1],
        ];

        foreach ($services as $service) {
            Service::create(array_merge($service, [
                'vat_percent' => 20.00,
                'duration_minutes' => 60,
                'is_active' => true,
            ]));
        }
    }
}