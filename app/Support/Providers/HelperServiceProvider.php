<?php

declare(strict_types=1);

namespace App\Support\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $helperBasePath = app_path('Support/Helpers');
        $helperFiles = glob($helperBasePath.'/*.php');

        foreach ($helperFiles as $file) {
            require_once $file;
        }
    }
}
