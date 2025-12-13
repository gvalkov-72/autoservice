<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'sku'           => 'OIL-5W30-1L',
            'name'          => 'Моторно масло 5W-30 1л',
            'brand'         => 'Mobil 1',
            'description'   => 'Синтетично масло',
            'unit'          => 'л.',
            'price'         => 25.00,
            'cost_price'    => 18.00,
            'vat_percent'   => 20,
            'stock_quantity'=> 50,
            'min_stock_level'=> 5,
            'location'      => 'A1-01',
        ]);

        Product::create([
            'sku'           => 'FIL-OC593',
            'name'          => 'Маслен филтър',
            'brand'         => 'MANN',
            'description'   => 'Филтър за масло',
            'unit'          => 'бр.',
            'price'         => 12.00,
            'cost_price'    => 7.50,
            'vat_percent'   => 20,
            'stock_quantity'=> 30,
            'min_stock_level'=> 3,
            'location'      => 'B2-05',
        ]);
    }
}