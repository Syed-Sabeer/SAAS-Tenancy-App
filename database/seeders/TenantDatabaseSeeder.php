<?php

namespace Database\Seeders;

use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Support\TenantUserRoles;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'profile.view',
            'profile.update',
            'dashboard.view',
        ];

        foreach ($permissions as $permissionName) {
            Permission::updateOrCreate(
                ['name' => $permissionName, 'guard_name' => 'tenant'],
                []
            );
        }

        $adminRole = Role::updateOrCreate(
            ['name' => TenantUserRoles::COMPANY_ADMIN, 'guard_name' => 'tenant'],
            []
        );

        $userRole = Role::updateOrCreate(
            ['name' => TenantUserRoles::COMPANY_USER, 'guard_name' => 'tenant'],
            []
        );

        $adminPermissionIds = Permission::whereIn('name', $permissions)->pluck('id')->all();
        $basicPermissionIds = Permission::whereIn('name', ['profile.view', 'profile.update', 'dashboard.view'])->pluck('id')->all();

        $adminRole->permissions()->sync($adminPermissionIds);
        $userRole->permissions()->sync($basicPermissionIds);
    }
}