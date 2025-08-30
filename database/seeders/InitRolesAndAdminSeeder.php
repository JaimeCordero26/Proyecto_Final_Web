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
        foreach ($roles as $r) {
            Role::findOrCreate($r, 'web'); // 👈 guard explícito
        }

        $entities = ['user','product','category','order'];
        $abilities = ['view','create','update','delete'];

        foreach ($entities as $e) {
            foreach ($abilities as $a) {
                Permission::findOrCreate("$e.$a", 'web'); // 👈 guard explícito
            }
        }

        Role::findByName('admin', 'web')->syncPermissions(Permission::all());

        Role::findByName('manager', 'web')->syncPermissions(
            Permission::whereIn('name', [
                'product.view','product.create','product.update',
                'category.view','category.create','category.update',
                'order.view','order.update',
                'user.view'
            ])->get()
        );

        Role::findByName('viewer', 'web')->syncPermissions(
            Permission::whereIn('name', [
                'product.view','category.view','order.view','user.view'
            ])->get()
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@tienda.com'],
            [
                'name' => 'Admin Principal',
                'password' => Hash::make('admin12345'),
            ]
        );

        $admin->syncRoles(['super_admin']);
    }
}
