<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,

    // --- Custom ---
    App\Support\Providers\SpatiePrefixedIdServiceProvider::class,
    App\Support\Providers\HelperServiceProvider::class,
    App\Support\Providers\AbstractConcreteServiceProvider::class,
];
