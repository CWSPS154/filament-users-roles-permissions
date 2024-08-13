<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentUsersRolesPermissions\Database\Seeders;

use App\Models\User;
use CWSPS154\FilamentUsersRolesPermissions\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'mobile' => '1234567890',
                'email_verified_at' => true,
                'password' => Hash::make('admin@123'),
                'is_admin' => true,
                'role_id' => Role::where('identifier','admin')->select('id')->first()->id,
                'is_active' => true,
            ]
        );
    }
}
