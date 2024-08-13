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
        Permission::create([
            'id' => '9ca5e9cf-f3d5-4eee-a863-2ad65d482fa2',
            'name' => 'Permission',
            'identifier' => 'permission',
            'route' => null,
            'parent_id' => null,
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5e9cf-ff2e-40a6-b35a-00660a0a53fe',
            'name' => 'View Permission',
            'identifier' => 'view-permission',
            'route' => 'filament.admin.user-manager.resources.permissions.index',
            'parent_id' => '9ca5e9cf-f3d5-4eee-a863-2ad65d482fa2',
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5ea51-9fbe-47bd-9e7e-72bc4906b20f',
            'name' => 'Role',
            'identifier' => 'role',
            'route' => null,
            'parent_id' => null,
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5ea51-ae8c-4936-994f-515ef52cfc21',
            'name' => 'View Role',
            'identifier' => 'view-role',
            'route' => 'filament.admin.user-manager.resources.roles.index',
            'parent_id' => '9ca5ea51-9fbe-47bd-9e7e-72bc4906b20f',
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5ea51-bafb-4f8a-9dc5-4dee6e4e37d4',
            'name' => 'Create Role',
            'identifier' => 'create-role',
            'route' => null,
            'parent_id' => '9ca5ea51-9fbe-47bd-9e7e-72bc4906b20f',
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5ea51-c6aa-4e59-b72c-c14dc093c930',
            'name' => 'Edit Role',
            'identifier' => 'edit-role',
            'route' => null,
            'parent_id' => '9ca5ea51-9fbe-47bd-9e7e-72bc4906b20f',
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5ea51-cfaf-4c4b-b176-79b9a1ad2f44',
            'name' => 'Delete Role',
            'identifier' => 'delete-role',
            'route' => null,
            'parent_id' => '9ca5ea51-9fbe-47bd-9e7e-72bc4906b20f',
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5eab5-9c8a-44df-b081-299eb890f314',
            'name' => 'User',
            'identifier' => 'user',
            'route' => null,
            'parent_id' => null,
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5eab5-a58d-4504-8807-990d18723be9',
            'name' => 'View User',
            'identifier' => 'view-user',
            'route' => 'filament.admin.user-manager.resources.users.index',
            'parent_id' => '9ca5eab5-9c8a-44df-b081-299eb890f314',
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5eab5-ae70-433e-b879-6334f78813d8',
            'name' => 'Create User',
            'identifier' => 'create-user',
            'route' => null,
            'parent_id' => '9ca5eab5-9c8a-44df-b081-299eb890f314',
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5eab5-bd25-4f86-b467-400ba9775292',
            'name' => 'Edit User',
            'identifier' => 'edit-user',
            'route' => null,
            'parent_id' => '9ca5eab5-9c8a-44df-b081-299eb890f314',
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);

        Permission::create([
            'id' => '9ca5eab5-c89e-4211-aea1-0382d5dffd03',
            'name' => 'Delete User',
            'identifier' => 'delete-user',
            'route' => null,
            'parent_id' => '9ca5eab5-9c8a-44df-b081-299eb890f314',
            'status' => 1,
            'created_at' => '2024-08-02 17:30:41',
            'updated_at' => '2024-08-02 17:30:41'
        ]);
    }
}
