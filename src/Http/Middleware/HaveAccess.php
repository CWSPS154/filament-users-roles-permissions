<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

namespace CWSPS154\FilamentUsersRolesPermissions\Http\Middleware;

use Closure;
use CWSPS154\FilamentUsersRolesPermissions\Models\Permission;
use CWSPS154\FilamentUsersRolesPermissions\Models\RolePermission;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HaveAccess
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (Auth::user()->is_admin || (Auth::user()->role_id && Auth::user()->role->all_permission)) {
                return $next($request);
            }

            $currentRouteName = $request->route()->getName();
            $permission = Permission::where('route', $currentRouteName)
                ->whereStatus(true)
                ->first();

            if (!$permission) {
                return $next($request);
            }

            $rolePermission = RolePermission::where('role_id', Auth::user()->role_id)
                ->where('permission_id', $permission->id)
                ->first();
            if (!$rolePermission) {
                Notification::make()
                    ->title(__('Warning'))
                    ->body(__("filament-users-roles-permissions::users-roles-permissions.have-access-page"))
                    ->warning()
                    ->send();
                return back();
            }
        }
        return $next($request);
    }
}
