<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create([
            'type'           => 'company',
            'name'           => 'Автоцентър ООД',
            'vat_number'     => 'BG123456789',
            'contact_person' => 'Георги Георгиев',
            'phone'          => '0888123456',
            'email'          => 'georgi@autocenter.bg',
            'address'        => 'ул. Индустриална 1',
            'city'           => 'София',
        ]);

        Customer::create([
            'type'           => 'individual',
            'name'           => 'Петър Петров',
            'vat_number'     => null,
            'contact_person' => null,
            'phone'          => '0888999988',
            'email'          => 'peter@abv.bg',
            'address'        => 'жк. Люлин, бл. 42',
            'city'           => 'София',
        ]);
    }
}