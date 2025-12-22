<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $modules = [
            'dashboard',
            'users',
            'centers',
            'members',
            'permissions',
            'roles',
        ];

        $actions = ['create', 'view', 'edit', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {

                $permissionName = "{$action}-{$module}";
                $desc = "Can {$action} {$module}";

                Permission::firstOrCreate(
                    [
                        'name' => $permissionName,
                        'guard_name' => 'api',
                    ],
                    [
                        'module' => $module,
                        'desc'   => $desc,
                    ]
                );
            }

            // ✅ EXTRA permission only for dashboard
            if ($module === 'dashboard') {
                Permission::firstOrCreate(
                    [
                        'name' => 'dashboard-stats',
                        'guard_name' => 'api',
                    ],
                    [
                        'module' => 'dashboard',
                        'desc'   => 'Can view dashboard statistics',
                    ]
                );
            }
        }
    }
}
