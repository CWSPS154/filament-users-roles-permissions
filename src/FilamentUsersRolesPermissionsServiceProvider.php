<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\FilamentUsersRolesPermissions;

use App\Models\User;
use CWSPS154\FilamentUsersRolesPermissions\Database\Seeders\DatabaseSeeder;
use CWSPS154\FilamentUsersRolesPermissions\Http\Middleware\HaveAccess;
use CWSPS154\FilamentUsersRolesPermissions\Models\Permission;
use CWSPS154\FilamentUsersRolesPermissions\Models\RolePermission;
use ErlandMuchasaj\LaravelGzip\Middleware\GzipEncodeResponse;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class FilamentUsersRolesPermissionsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-users-roles-permissions';

    public function configurePackage(Package $package): void
    {
        $package->name(self::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations(
                [
                    'alter_table_user',
                    'alter_media_table',
                    'create_permissions_table',
                    'create_role_permissions_table',
                    'create_roles_table'
                ]
            )
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Hi Mate, Thank you for installing My Package!');
                    })
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->endWith(function (InstallCommand $command) {
                        if ($command->confirm('Do you wish to publish spatie media provider ?')) {
                            $command->call('vendor:publish', [
                                'provider' => MediaLibraryServiceProvider::class
                            ]);
                        }
                        if ($command->confirm('Do you wish to run the seeder ?')) {
                            $command->comment('The seeder is filled with "admin" as panel id, please check the route name for your panel');
                            $command->comment('Running seeder...');
                            $command->call('db:seed', [
                                'class' => DatabaseSeeder::class
                            ]);
                        }
                        $command->info('I hope this package will help you to build user management system');
                    })
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('CWSPS154/filament-users-roles-permissions');
            });
    }

    /**
     * @return FilamentUsersRolesPermissionsServiceProvider
     */
    public function boot(): FilamentUsersRolesPermissionsServiceProvider
    {
        $this->configureMiddleware();
        Gate::define('have-access', function (User $user, string|array $identifiers = null) {
            if ($user->is_admin || ($user->role_id && $user->role->all_permission)) {
                return true;
            }
            if (!is_array($identifiers)) {
                $identifiers = explode(',', $identifiers);
            }
            $permissions = Permission::whereIn('identifier', $identifiers)
                ->where('status', true)
                ->pluck('id');
            if ($permissions && !RolePermission::where('role_id', $user->role_id)
                    ->whereIn('permission_id', $permissions)
                    ->exists()) {
                return false;
            }
            return true;
        });
        return parent::boot();
    }

    /**
     * @return void
     */
    protected function configureMiddleware(): void
    {
        $this->app->booted(function () {
            $kernel = $this->app->make(Kernel::class);
            $kernel->appendMiddlewareToGroup('web', [
                HaveAccess::class,
                GzipEncodeResponse::class
            ]);
        });
    }
}
