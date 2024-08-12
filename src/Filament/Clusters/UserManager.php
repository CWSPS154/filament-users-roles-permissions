<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters;

use CWSPS154\FilamentUsersRolesPermissions\FilamentUsersRolesPermissionsServiceProvider;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class UserManager extends Cluster
{
    public const DEFAULT_DATETIME_FORMAT = 'M-d-Y h:i:s A';

    public function getLayout(): string
    {
        if (config('filament-users-roles-permissions.user_manager.layout')) {
            return config('filament-users-roles-permissions.user_manager.layout');
        }
        return parent::getLayout();
    }

    public static function getNavigationLabel(): string
    {
        return __(config('filament-users-roles-permissions.user_manager.navigation.label'));
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return config('filament-users-roles-permissions.user_manager.navigation.icon');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-users-roles-permissions.user_manager.navigation.group'));
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-users-roles-permissions.user_manager.sort');
    }

    public static function checkAccess(string $method, Model $record = null): bool
    {
        $plugin = Filament::getCurrentPanel()?->getPlugin(FilamentUsersRolesPermissionsServiceProvider::$name);
        $access = $plugin->$method();

        if (!empty($access) && is_array($access) && isset($access['ability'], $access['arguments'])) {
            return Gate::allows($access['ability'], $access['arguments']);
        }

        return $access;
    }
}
