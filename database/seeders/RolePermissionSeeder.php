<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view calculations',
            'view results',
            'manage criterias',
            'manage weights',
            'manage students',
            'manage scores',
            'manage users',
            'manage classes',
            'manage periods',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'headmaster' => [
                'view calculations',
                'view results',
            ],
            'staff_student' => [
                'manage criterias',
                'manage weights',
                'view calculations',
                'view results',
            ],
            'homeroom_teacher' => [
                'manage students',
                'manage scores',
            ],
            'administration' => [
                'manage students',
                'manage criterias',
                'manage weights',
                'view calculations',
                'view results',
                'manage users',
                'manage classes',
                'manage periods',
            ],
        ];

        foreach ($roles as $role => $perms) {
            $roleModel = Role::firstOrCreate(['name' => $role]);
            $roleModel->syncPermissions($perms);
        }
    }
}
