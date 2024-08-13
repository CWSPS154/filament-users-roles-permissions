<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager\Resources;

use CWSPS154\FilamentUsersRolesPermissions\Exports\PermissionExporter;
use CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager;
use CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager\Resources\PermissionResource\Pages\ManagePermissions;
use CWSPS154\FilamentUsersRolesPermissions\Models\Permission;
use CWSPS154\FilamentUsersRolesPermissions\Rules\RouteHas;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $cluster = UserManager::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('children');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('identifier', Str::slug($state))),
                Forms\Components\TextInput::make('identifier')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.identifier'))
                    ->required()
                    ->disabled()
                    ->maxLength(255)
                    ->dehydrated()
                    ->unique(Permission::class, 'identifier', ignoreRecord: true),
                Repeater::make('children')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.children'))
                    ->relationship('children')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.name'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, Forms\Set $set) => $set('identifier', Str::slug($state))),
                        Forms\Components\TextInput::make('identifier')
                            ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.identifier'))
                            ->required()
                            ->disabled()
                            ->maxLength(255)
                            ->dehydrated()
                            ->columnSpan(['default' => 2, 'sm' => 1])
                            ->unique(Permission::class, 'identifier', ignoreRecord: true),
                        Forms\Components\TextInput::make('route')
                            ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.route'))
                            ->maxLength(255)
                            ->dehydrated()
                            ->rules([new RouteHas()])
                            ->unique(Permission::class, 'route', ignoreRecord: true)->columnSpan(2),
                        Forms\Components\Toggle::make('status')
                            ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.status'))
                            ->inline(false)
                            ->required()
                            ->default(true),
                    ])->columnSpanFull(),
                Forms\Components\Toggle::make('status')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.status'))
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.identifier'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.status'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.table.created.at'))
                    ->dateTime(UserManager::DEFAULT_DATETIME_FORMAT)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.table.updated.at'))
                    ->dateTime(UserManager::DEFAULT_DATETIME_FORMAT)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver()->hiddenLabel(),
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make()
                ])
            ])
            ->headerActions(
                ActionGroup::make([
                    ExportAction::make()
                        ->exporter(PermissionExporter::class)->visible(function () {
                            return UserManager::checkAccess('getCanCreatePermission') && UserManager::checkAccess('getCanEditPermission');
                        })
                ])->icon('heroicon-o-circle-stack')
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(function () {
                        return UserManager::checkAccess('getCanDeletePermission');
                    }),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.name')),
                TextEntry::make('identifier')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.identifier')),
                IconEntry::make('status')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.status'))
                    ->boolean(),
                RepeatableEntry::make('children')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.children'))
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.name')),
                        TextEntry::make('identifier')
                            ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.identifier')),
                        IconEntry::make('status')
                            ->boolean()
                            ->label(__('filament-users-roles-permissions::users-roles-permissions.permission.resource.form.status')),
                    ])->grid()
            ])->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePermissions::route('/'),
        ];
    }

    public function getLayout(): string
    {
        if (config('filament-users-roles-permissions.user_manager.permission_resource.layout')) {
            return config('filament-users-roles-permissions.user_manager.permission_resource.layout');
        }
        return parent::getLayout();
    }

    public static function getNavigationLabel(): string
    {
        return __(config('filament-users-roles-permissions.user_manager.permission_resource.navigation.label'));
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return config('filament-users-roles-permissions.user_manager.permission_resource.navigation.icon');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-users-roles-permissions.user_manager.permission_resource.navigation.group'));
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-users-roles-permissions.user_manager.permission_resource.navigation.sort');
    }

    public static function canViewAny(): bool
    {
        return UserManager::checkAccess('getCanViewAnyPermission');
    }

    public static function canCreate(): bool
    {
        return UserManager::checkAccess('getCanCreatePermission') ? auth()->user()->is_admin : false;
    }

    public static function canEdit(Model $record): bool
    {
        return UserManager::checkAccess('getCanEditPermission', $record) ? auth()->user()->is_admin : false;
    }

    public static function canDelete(Model $record): bool
    {
        return UserManager::checkAccess('getCanDeletePermission', $record) ? auth()->user()->is_admin : false;
    }
}
