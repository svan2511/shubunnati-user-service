<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // ✅ Create admin role with API guard
        $adminRole = Role::firstOrCreate(
            [
                'name'       => 'admin',
                'guard_name' => 'api',   // 🔥 CRITICAL
            ],
            [
                'desc' => 'This is for Admin User Role',
            ]
        );

        // ✅ Assign ONLY api permissions
        $permissions = Permission::where('guard_name', 'api')->get();
        $adminRole->syncPermissions($permissions);

        // ✅ Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        // ✅ Assign role object (not string)
        if (! $admin->hasRole($adminRole)) {
            $admin->assignRole($adminRole);
        }
    }
}
