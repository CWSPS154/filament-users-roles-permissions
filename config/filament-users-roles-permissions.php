<?php
/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

return [
    'user_manager' => [
        'layout' => null,
        'navigation' => [
            'title' => 'filament-users-roles-permissions::users-roles-permissions.user.manager',
            'group' => 'filament-users-roles-permissions::users-roles-permissions.system',
            'label' => 'filament-users-roles-permissions::users-roles-permissions.user.manager',
            'icon' => 'heroicon-o-user-group',
            'sort' => 100,
        ],
        'user_resource' => [
            'layout' => null,
            'navigation' => [
                'title' => 'filament-users-roles-permissions::users-roles-permissions.user.resource.user.title',
                'group' => null,
                'label' => 'filament-users-roles-permissions::users-roles-permissions.user.resource.user',
                'icon' => 'heroicon-o-users',
                'sort' => 1,
            ]
        ],
        'role_resource' => [
            'layout' => null,
            'navigation' => [
                'title' => 'filament-users-roles-permissions::users-roles-permissions.role.resource.role.title',
                'group' => null,
                'label' => 'filament-users-roles-permissions::users-roles-permissions.role.resource.role',
                'icon' => 'heroicon-o-academic-cap',
                'sort' => 2,
            ]
        ],
        'permission_resource' => [
            'layout' => null,
            'navigation' => [
                'title' => 'filament-users-roles-permissions::users-roles-permissions.permission.resource.permission.title',
                'group' => null,
                'label' => 'filament-users-roles-permissions::users-roles-permissions.permission.resource.permission',
                'icon' => 'heroicon-o-finger-print',
                'sort' => 3,
            ]
        ]
    ]
];
