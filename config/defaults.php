<?php

declare(strict_types=1);

return [
    'settings' => [
        'tbl_column' => 'settings',
        'tbl_value' => [
            // User Defined Settings
            'appearance' => [
                'theme' => 'system',
                'locale' => 'en',
                'display_timezone' => env('TIMEZONE', 'UTC'),
            ],
            // System Settings
            'application' => [
                'system_timezone' => env('TIMEZONE', 'UTC'),
            ],
        ],
    ],
];
