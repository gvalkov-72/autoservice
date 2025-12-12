<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view customers',
            'edit customers',
            'view vehicles',
            'edit vehicles',
            'view products',
            'edit products',
            'view work orders',
            'edit work orders',
            'view invoices',
            'edit invoices',
            'admin',
        ];

        foreach ($permissions as $p) {
            Permission::create(['name' => $p]);
        }

        $admin = Role::create(['name' => 'admin']);
        $cashier = Role::create(['name' => 'cashier']);
        $mechanic = Role::create(['name' => 'mechanic']);

        $admin->givePermissionTo(Permission::all());
        $cashier->givePermissionTo(['view customers', 'view vehicles', 'view products', 'view work orders', 'view invoices', 'edit invoices']);
        $mechanic->givePermissionTo(['view customers', 'view vehicles', 'view work orders', 'edit work orders']);
    }
}