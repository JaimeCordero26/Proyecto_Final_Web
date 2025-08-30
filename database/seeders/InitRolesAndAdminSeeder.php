<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InitRolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['super_admin', 'admin', 'manager', 'viewer', 'customer'];
        foreach ($roles as $r) Role::findOrCreate($r);

        $entities = ['user','product','category','order'];
        $abilities = ['view','create','update','delete'];
        foreach ($entities as $e) {
            foreach ($abilities as $a) {
                Permission::findOrCreate("$e.$a");
            }
        }

        Role::findByName('admin')->syncPermissions(Permission::all());
        Role::findByName('manager')->syncPermissions(
            Permission::whereIn('name', [
                'product.view','product.create','product.update',
                'category.view','category.create','category.update',
                'order.view','order.update',
                'user.view'
            ])->pluck('name')->toArray()
        );
        Role::findByName('viewer')->syncPermissions(
            Permission::whereIn('name', [
                'product.view','category.view','order.view','user.view'
            ])->pluck('name')->toArray()
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@tienda.test'],
            ['name' => 'Admin Principal', 'password' => Hash::make('Admin12345')]
        );

        $admin->syncRoles(['super_admin']);
    }
}
