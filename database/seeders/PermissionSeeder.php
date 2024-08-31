<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentUsersRolesPermissions\Database\Seeders;

use CWSPS154\FilamentUsersRolesPermissions\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $id = Permission::create([
            'name' => 'Permission',
            'identifier' => 'permission',
            'route' => null,
            'parent_id' => null,
            'status' => true
        ])->id;

        Permission::create([
            'name' => 'View Permission',
            'identifier' => 'view-permission',
            'route' => 'filament.admin.user-manager.resources.permissions.index',
            'parent_id' => $id,
            'status' => true
        ]);

        $roleId = Permission::create([
            'name' => 'Role',
            'identifier' => 'role',
            'route' => null,
            'parent_id' => null,
            'status' => true
        ])->id;

        Permission::create([
            'name' => 'View Role',
            'identifier' => 'view-role',
            'route' => 'filament.admin.user-manager.resources.roles.index',
            'parent_id' => $roleId,
            'status' => true
        ]);

        Permission::create([
            'name' => 'Create Role',
            'identifier' => 'create-role',
            'route' => null,
            'parent_id' => $roleId,
            'status' => true
        ]);

        Permission::create([
            'name' => 'Edit Role',
            'identifier' => 'edit-role',
            'route' => null,
            'parent_id' => $roleId,
            'status' => true
        ]);

        Permission::create([
            'name' => 'Delete Role',
            'identifier' => 'delete-role',
            'route' => null,
            'parent_id' => $roleId,
            'status' => true
        ]);

        $userId = Permission::create([
            'name' => 'User',
            'identifier' => 'user',
            'route' => null,
            'parent_id' => null,
            'status' => true
        ])->id;

        Permission::create([
            'name' => 'View User',
            'identifier' => 'view-user',
            'route' => 'filament.admin.user-manager.resources.users.index',
            'parent_id' => $userId,
            'status' => true
        ]);

        Permission::create([
            'name' => 'Create User',
            'identifier' => 'create-user',
            'route' => null,
            'parent_id' => $userId,
            'status' => true
        ]);

        Permission::create([
            'name' => 'Edit User',
            'identifier' => 'edit-user',
            'route' => null,
            'parent_id' => $userId,
            'status' => true
        ]);

        Permission::create([
            'name' => 'Delete User',
            'identifier' => 'delete-user',
            'route' => null,
            'parent_id' => $userId,
            'status' => true
        ]);
    }
}
