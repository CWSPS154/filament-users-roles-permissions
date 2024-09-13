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
use Illuminate\Support\Facades\File;
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
                        $command->info('Hi Mate, Thank you for installing Filament Users Roles Permissions.!');
                        $command->comment('Publishing spatie media provider...');
                        $command->call('vendor:publish', [
                            '--provider' => MediaLibraryServiceProvider::class
                        ]);
                        $this->addTraitAndInterfaceToUser($command);
                    })
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->endWith(function (InstallCommand $command) {
                        if ($command->confirm('Do you wish to run the seeder ?')) {
                            $command->comment('The seeder is filled with "admin" as panel id, please check the route name for your panel');
                            $command->comment('Running seeder...');
                            $command->call('db:seed', [
                                'class' => DatabaseSeeder::class
                            ]);
                        }
                        $command->info('I hope this package will help you to build user management system');
                    })
                    ->askToStarRepoOnGitHub('CWSPS154/filament-users-roles-permissions');
            });
    }

    /**
     * @param InstallCommand $command
     * @return void
     */
    protected function addTraitAndInterfaceToUser(InstallCommand $command): void
    {
        $userModelPath = app_path('Models/User.php');
        if (!File::exists($userModelPath)) {
            $command->error('User model not found!');
            return;
        }
        $modelContent = File::get($userModelPath);
        $modelContent = $this->addTraitToModel($modelContent, $command);
        $modelContent = $this->addInterfacesToModel($modelContent, $command);
        $modelContent = $this->updateFillable($modelContent, $command);
        $modelContent = $this->updateHidden($modelContent, $command);
        $modelContent = $this->updateCasts($modelContent, $command);
        File::put($userModelPath, $modelContent);
        $command->info('User model updated successfully.');
    }

    /**
     * @param string $modelContent
     * @param InstallCommand $command
     * @return string
     */
    protected function addTraitToModel(string $modelContent, InstallCommand $command): string
    {
        $traitClass = 'use CWSPS154\FilamentUsersRolesPermissions\Models\HasRole;';
        $trait = 'use HasRole;';
        if (!str_contains($modelContent, $traitClass)) {
            $modelContent = preg_replace(
                '/namespace\s+[^;]+;/',
                "$0\n\n.'       '.$traitClass",
                $modelContent
            );
            if (!str_contains($modelContent, $trait)) {
                $modelContent = preg_replace(
                    '/class\s+[^;]+;/',
                    "$0\n\n.'       '.$trait",
                    $modelContent
                );
                $command->info('Trait added successfully.');
            }
        } else {
            $command->info('Trait already exists.');
        }
        return $modelContent;
    }

    /**
     * @param string $modelContent
     * @param InstallCommand $command
     * @return string
     */
    protected function addInterfacesToModel(string $modelContent, InstallCommand $command): string
    {
        $interfaces = [
            '\Spatie\MediaLibrary\HasMedia',
            '\Filament\Models\Contracts\HasAvatar',
            '\Filament\Models\Contracts\FilamentUser',
            '\Illuminate\Contracts\Auth\MustVerifyEmail',
        ];

        $interfacesToAdd = implode(', ', $interfaces);
        if (preg_match('/class\s+User\s+extends\s+Authenticatable\s+\w+(\s+implements\s+([^\{]+))?/', $modelContent, $matches)) {
            $existingInterfaces = $matches[2] ?? '';
            $existingInterfacesArray = array_map('trim', explode(',', $existingInterfaces));
            $existingInterfacesArray = array_filter($existingInterfacesArray);
            $newInterfacesArray = array_diff($interfaces, $existingInterfacesArray);
            if (!empty($newInterfacesArray)) {
                $newInterfacesString = implode(', ', array_merge($existingInterfacesArray, $newInterfacesArray));
                $modelContent = preg_replace(
                    '/class\s+User\s+extends\s+\w+(\s+implements\s+[^\{]*)?/',
                    "class User extends Authenticatable implements $newInterfacesString",
                    $modelContent
                );
                $command->info('Interfaces added successfully.');
            } else {
                $command->info('Interfaces already exist.');
            }
        } else {
            $modelContent = preg_replace(
                '/class\s+User\s+extends\s+\w+/',
                "class User extends Authenticatable implements $interfacesToAdd",
                $modelContent
            );
            $command->info('Interfaces added successfully.');
        }
        return $modelContent;
    }

    /**
     * @param string $modelContent
     * @param InstallCommand $command
     * @return string
     */
    protected function updateFillable(string $modelContent, InstallCommand $command): string
    {
        $newFillable = <<<EOT
        /**
        * The attributes that are mass assignable.
        *
        * @var array<int, string>
        */
        protected \$fillable = [
               'name',
               'email',
               'mobile',
               'password',
               'role_id',
               'last_seen',
               'is_active'
        ];
        EOT;

        $modelContent = preg_replace(
            '/\/\*\*\s+\*\sThe attributes that are mass assignable\..+?protected\s+\$fillable\s+=\s+\[.*?\];/s',
            $newFillable,
            $modelContent
        );
        $command->info('Fillable updated successfully.');
        return $modelContent;
    }

    /**
     * @param string $modelContent
     * @param InstallCommand $command
     * @return string
     */
    protected function updateHidden(string $modelContent, InstallCommand $command): string
    {
        $newHidden = <<<EOT
        /**
        * The attributes that should be hidden for serialization.
        *
        * @var array<int, string>
        */
        protected \$hidden = [
               'password',
               'remember_token',
       ];
       EOT;
        $modelContent = preg_replace(
            '/\/\*\*\s+\*\sThe attributes that should be hidden for serialization\..+?protected\s+\$hidden\s+=\s+\[.*?\];/s',
            $newHidden,
            $modelContent
        );
        $command->info('Hidden attributes updated successfully.');
        return $modelContent;
    }


    /**
     * @param string $modelContent
     * @param InstallCommand $command
     * @return string
     */
    protected function updateCasts(string $modelContent, InstallCommand $command): string
    {
        $newCasts = <<<EOT
        /**
         * Get the attributes that should be cast.
         *
         * @return array<string, string>
         */
        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
                'last_seen' => 'datetime',
                'is_active' => 'boolean',
            ];
        }
        EOT;
        $modelContent = preg_replace(
            '/\/\*\*\s+\*\sGet the attributes that should be cast\..+?protected\s+function\s+casts\(\):\s+array\s+\{.*?\};/s',
            $newCasts,
            $modelContent
        );
        $command->info('Casts updated successfully.');
        return $modelContent;
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
