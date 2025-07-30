<?php

declare(strict_types=1);

use App\Domains\Shared\Models\Organization;

return [
    'role' => [
        // guard
        'web' => [
            // name => hierarchy
            'org_master' => 3,
            'org_admin' => 4,
            'org_manager' => 5,
            'org_user' => 6,
        ],
        'system' => [
            'sys_master' => 1,
            'sys_admin' => 2,
        ],
    ],
    'permission' => [
        'class_with_permissions' => [
            Organization::class,
        ],
    ],
];
