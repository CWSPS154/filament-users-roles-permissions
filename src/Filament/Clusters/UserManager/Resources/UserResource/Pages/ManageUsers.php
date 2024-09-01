<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager\Resources\UserResource\Pages;

use CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __(config('filament-users-roles-permissions.user_manager.user_resource.navigation.title'));
    }
}
