<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

declare(strict_types=1);

namespace CWSPS154\FilamentUsersRolesPermissions;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentUsersRolesPermissionsPlugin implements Plugin
{
    use EvaluatesClosures;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canViewAnyUser = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canCreateUser = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canEditUser = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canDeleteUser = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canViewAnyRole = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canCreateRole = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canEditRole = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canDeleteRole = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canViewAnyPermission = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canCreatePermission = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canEditPermission = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canDeletePermission = true;

    /**
     * @var bool|Closure|array
     */
    protected Closure|array|bool $canAccessEditProfile = true;

    public function getId(): string
    {
        return FilamentUsersRolesPermissionsServiceProvider::$name;
    }

    public function register(Panel $panel): void
    {
        $panel->discoverClusters(
            in: __DIR__.'/Filament/Clusters',
            for: 'CWSPS154\\FilamentUsersRolesPermissions\\Filament\\Clusters'
        );
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }

    public static function make(): static
    {
        return app(static::class);
    }

    protected function setAbility(mixed $ability, mixed $arguments = null): array|bool
    {
        if ($ability instanceof Closure) {
            return $this->evaluate($ability);
        }

        if (is_string($ability) && !is_null($arguments)) {
            return [
                'ability' => $ability,
                'arguments' => $arguments,
            ];
        }

        return (bool)$ability;
    }

    public function canViewAnyUser(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canViewAnyUser = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanViewAnyUser(): array|bool
    {
        return $this->canViewAnyUser;
    }

    public function canCreateUser(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canCreateUser = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanCreateUser(): array|bool
    {
        return $this->canCreateUser;
    }

    public function canEditUser(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canEditUser = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanEditUser(): array|bool
    {
        return $this->canEditUser;
    }

    public function canDeleteUser(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canDeleteUser = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanDeleteUser(): array|bool
    {
        return $this->canDeleteUser;
    }

    public function canViewAnyRole(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canViewAnyRole = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanViewAnyRole(): array|bool
    {
        return $this->canViewAnyRole;
    }

    public function canCreateRole(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canCreateRole = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanCreateRole(): array|bool
    {
        return $this->canCreateRole;
    }

    public function canEditRole(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canEditRole = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanEditRole(): array|bool
    {
        return $this->canEditRole;
    }

    public function canDeleteRole(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canDeleteRole = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanDeleteRole(): array|bool
    {
        return $this->canDeleteRole;
    }

    public function canViewAnyPermission(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canViewAnyPermission = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanViewAnyPermission(): array|bool
    {
        return $this->canViewAnyPermission;
    }

    public function canCreatePermission(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canCreatePermission = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanCreatePermission(): array|bool
    {
        return $this->canCreatePermission;
    }

    public function canEditPermission(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canEditPermission = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanEditPermission(): array|bool
    {
        return $this->canEditPermission;
    }

    public function canDeletePermission(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canDeletePermission = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanDeletePermission(): array|bool
    {
        return $this->canDeletePermission;
    }

    public function canAccessEditProfile(bool|Closure|string $ability = true, $arguments = null): static
    {
        $this->canAccessEditProfile = $this->setAbility($ability, $arguments);
        return $this;
    }

    public function getCanAccessEditProfile(): array|bool
    {
        return $this->canAccessEditProfile;
    }
}
