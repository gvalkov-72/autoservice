<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name'  => 'Админ',
            'email' => 'admin@autoservice.local',
            'password' => Hash::make('123456'),
        ]);

        $user->assignRole('admin');
    }
}