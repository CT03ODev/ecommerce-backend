<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;


class InitUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('permissions:sync');
        Permission::create(['name' => 'access-panel']);

        foreach (Role::$ROLES as $roleKey => $roleName) {
            $role = Role::create(['name' => $roleName]);
            switch ($roleKey) {
                case 'ADMIN':
                    $role->givePermissionTo(Permission::where('name', 'not like', '%Permission%')->where('name', 'not like', '%Role%')->get());
                    break;
                case 'CONTENT_CREATOR':
                    $role->givePermissionTo('access-panel');
                    $role->givePermissionTo(Permission::where('name', 'like', '%Blog%')->get());
                    break;
                case 'USER':
                    break;
            }
        }

        $users = [
            [
                'name' => 'Thanh Vo',
                'email' => 'vochithanh299@gmail.com',
                'password' => '$2y$10$is2C/lcStJi5VYYE5lMB0OqDvsKkeMjWYj3PPSItC/y/ACzi9eWve',
            ],
        ];
        foreach ($users as $userData) {
            $user = User::create($userData);
            $user->assignRole(Role::all());
        }
    }
}
