<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager\Resources;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager;
use CWSPS154\FilamentUsersRolesPermissions\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $cluster = UserManager::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('role')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.form.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('identifier', Str::slug($state))),
                Forms\Components\TextInput::make('identifier')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.form.identifier'))
                    ->required()
                    ->disabled()
                    ->maxLength(255)
                    ->dehydrated()
                    ->unique(Role::class, 'identifier', ignoreRecord: true),
                Forms\Components\Toggle::make('all_permission')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.form.all.permission'))
                    ->live()
                    ->default(true)
                    ->afterStateUpdated(function (Get $get, $state, Forms\Set $set) {
                        if ($get('all_permission')) {
                            $set('permission_id', []);
                        }
                    })
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.form.is.active'))
                    ->required()
                    ->default(true),
                SelectTree::make('permission_id')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.form.permissions'))
                    ->live()
                    ->relationship('permissions', 'name', 'parent_id', function ($query) {
                        return $query->where('status', true);
                    }, function ($query) {
                        return $query->where('status', true);
                    })
                    ->afterStateUpdated(function (Get $get, $state, Forms\Set $set) {
                        if ($get('all_permission')) {
                            $set('all_permission', !$state);
                        }
                    })
                    ->searchable()
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('role')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.form.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.form.identifier'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('all_permission')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.form.all.permission'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.form.permissions'))
                    ->default('-')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.form.is.active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.table.created.at'))
                    ->dateTime(UserManager::DEFAULT_DATETIME_FORMAT)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.role.resource.table.updated.at'))
                    ->dateTime(UserManager::DEFAULT_DATETIME_FORMAT)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions(
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make()
                ])
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(function () {
                        return UserManager::checkAccess('getCanDeleteRole');
                    }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => UserManager\Resources\RoleResource\Pages\ManageRoles::route('/')
        ];
    }

    public function getLayout(): string
    {
        if (config('filament-users-roles-permissions.user_manager.role_resource.layout')) {
            return config('filament-users-roles-permissions.user_manager.role_resource.layout');
        }
        return parent::getLayout();
    }

    public static function getNavigationLabel(): string
    {
        return __(config('filament-users-roles-permissions.user_manager.role_resource.navigation.label'));
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return config('filament-users-roles-permissions.user_manager.role_resource.navigation.icon');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-users-roles-permissions.user_manager.role_resource.navigation.group'));
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-users-roles-permissions.user_manager.role_resource.navigation.sort');
    }

    public static function canAccess(): bool
    {
        return UserManager::checkAccess('getCanAccess');
    }

    public static function canViewAny(): bool
    {
        return UserManager::checkAccess('getCanViewAnyRole');
    }

    public static function canCreate(): bool
    {
        return UserManager::checkAccess('getCanCreateRole');
    }

    public static function canEdit(Model $record): bool
    {
        return UserManager::checkAccess('getCanEditRole', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return UserManager::checkAccess('getCanDeleteRole', $record);
    }
}
