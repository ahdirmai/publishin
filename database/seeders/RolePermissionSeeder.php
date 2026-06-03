<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder {
    public function run(): void {
        // Flush cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Post permissions
            'post.create', 'post.read', 'post.update', 'post.delete', 'post.publish',
            // Analytics permissions
            'analytics.read',
            // Reports permissions
            'report.create', 'report.read', 'report.download',
            // Settings permissions
            'settings.read', 'settings.update',
            // Social accounts
            'social_account.connect', 'social_account.disconnect',
            // Team management
            'team.invite', 'team.manage',
            // Billing
            'billing.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Roles dengan permissions
        $roles = [
            'super_admin' => $permissions, // semua
            'agency_owner' => [
                'post.create', 'post.read', 'post.update', 'post.delete', 'post.publish',
                'analytics.read', 'report.create', 'report.read', 'report.download',
                'settings.read', 'settings.update',
                'social_account.connect', 'social_account.disconnect',
                'team.invite', 'team.manage', 'billing.manage',
            ],
            'agency_admin' => [
                'post.create', 'post.read', 'post.update', 'post.delete', 'post.publish',
                'analytics.read', 'report.create', 'report.read', 'report.download',
                'settings.read', 'social_account.connect', 'social_account.disconnect',
                'team.invite',
            ],
            'editor' => [
                'post.create', 'post.read', 'post.update',
                'analytics.read', 'report.read',
                'settings.read',
            ],
            'viewer' => [
                'post.read', 'analytics.read', 'report.read', 'settings.read',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
