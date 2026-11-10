<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $vaRole         = Role::firstOrCreate(['name' => 'VA']);
        $salesRole      = Role::firstOrCreate(['name' => 'Sales Officer']);

        // 2. Create Default Super Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@commission.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
            ]
        );

        // Assign Super Admin Role
        $admin->assignRole($superAdminRole);
    }
}