<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager\Resources;

use App\Models\User;
use CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager;
use CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager\Resources\UserResource\Pages\EditProfile;
use CWSPS154\FilamentUsersRolesPermissions\Filament\Clusters\UserManager\Resources\UserResource\Pages\ManageUsers;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $cluster = UserManager::class;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.email'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email', ignoreRecord: true),
                PhoneInput::make('mobile')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.mobile'))
                    ->required()
                    ->unique(User::class, 'mobile', ignoreRecord: true)
                    ->rules(['phone'])
                    ->ipLookup(function () {
                        return rescue(fn() => Http::get('https://ipinfo.io/json')->json('country'), app()->getLocale(), report: false);
                    })
                    ->displayNumberFormat(PhoneInputNumberType::NATIONAL),
                Forms\Components\Select::make('role_id')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.role'))
                    ->required()
                    ->relationship('role', 'role', function ($query) {
                        return $query->where('is_active', true);
                    })
                    ->native(false),
                Forms\Components\TextInput::make('password')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.password'))
                    ->password()
                    ->confirmed()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create')
                    ->maxLength(255),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.confirm.password'))
                    ->password()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.active'))
                    ->required(),
                SpatieMediaLibraryFileUpload::make('media')
                    ->collection('profile-images')
                    ->conversion('avatar')
                    ->image()
                    ->maxSize(2048)
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.profile.image'))
                    ->optimize('webp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('avatar')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.table.image'))
                    ->conversion('avatar')
                    ->defaultImageUrl(User::DEFAULT_IMAGE_URL)
                    ->collection('profile-images'),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.mobile'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('role.role')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.role'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.form.active'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('last_seen')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.table.online'))
                    ->icon(function (Model $model) {
                        if ($model->isOnline()) {
                            return 'heroicon-o-face-smile';
                        }
                        return 'heroicon-o-face-frown';
                    })
                    ->boolean(function (Model $model) {
                        return $model->isOnline();
                    })
                    ->default(false),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.table.verified'))
                    ->icon(function (Model $model) {
                        if ($model->email_verified_at) {
                            return 'heroicon-o-check';
                        }
                        return 'heroicon-o-x-mark';
                    })
                    ->default(false)
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.table.created.at'))
                    ->dateTime(UserManager::DEFAULT_DATETIME_FORMAT)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.table.updated.at'))
                    ->dateTime(UserManager::DEFAULT_DATETIME_FORMAT)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.table.deleted.at'))
                    ->dateTime(UserManager::DEFAULT_DATETIME_FORMAT)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.table.created.by'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('editor.name')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.table.updated.by'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('destroyer.name')
                    ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.table.deleted.by'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->native(false),
            ])
            ->actions(
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->slideOver()->visible(function ($record) {
                        return !(auth()->id() === $record->id);
                    }),
                    Tables\Actions\DeleteAction::make()->visible(function ($record) {
                        return !(auth()->id() === $record->id);
                    }),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\Action::make('Profile')
                        ->label(__('filament-users-roles-permissions::users-roles-permissions.user.resource.table.action.edit.profile'))
                        ->icon('heroicon-o-user')
                        ->url(EditProfile::getUrl())->visible(function ($record) {
                            if (UserManager::checkAccess('getCanEditUser', $record)) {
                                return auth()->id() === $record->id;
                            }
                            return false;
                        })
                ])
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(function () {
                        return UserManager::checkAccess('getCanDeleteUser');
                    }),
                    Tables\Actions\ForceDeleteBulkAction::make()->visible(function () {
                        return UserManager::checkAccess('getCanDeleteUser');
                    }),
                    Tables\Actions\RestoreBulkAction::make()->visible(function () {
                        return UserManager::checkAccess('getCanEditUser');
                    }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'mobile'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('filament-users-roles-permissions::users-roles-permissions.user.resource.form.email') => $record->email,
            __('filament-users-roles-permissions::users-roles-permissions.user.resource.form.mobile') => $record->mobile,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('deleted_at', null)->count();
    }

    public function getLayout(): string
    {
        if (config('filament-users-roles-permissions.user_manager.user_resource.layout')) {
            return config('filament-users-roles-permissions.user_manager.user_resource.layout');
        }
        return parent::getLayout();
    }

    public static function getNavigationLabel(): string
    {
        return __(config('filament-users-roles-permissions.user_manager.user_resource.navigation.label'));
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return config('filament-users-roles-permissions.user_manager.user_resource.navigation.icon');
    }

    public static function getNavigationGroup(): ?string
    {
        return __(config('filament-users-roles-permissions.user_manager.user_resource.navigation.group'));
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-users-roles-permissions.user_manager.user_resource.navigation.sort');
    }

    public static function canViewAny(): bool
    {
        return UserManager::checkAccess('getCanViewAnyUser');
    }

    public static function canCreate(): bool
    {
        return UserManager::checkAccess('getCanCreateUser');
    }

    public static function canEdit(Model $record): bool
    {
        return UserManager::checkAccess('getCanEditUser', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return UserManager::checkAccess('getCanDeleteUser', $record);
    }
}
