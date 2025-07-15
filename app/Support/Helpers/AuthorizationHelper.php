<?php

declare(strict_types=1);

if (! function_exists('getModelForGuard')) {
    function getModelForGuard(string $guard): ?string
    {
        return App\Domains\Shared\Domains\Authorization\Guard::getModelForGuard($guard);
    }
}

if (! function_exists('activeGuard')) {
    function activeGuard(): int|string|null
    {
        foreach (array_keys(config('auth.guards')) as $guard) {
            if (auth()->guard($guard)->check()) {
                return $guard;
            }
        }

        return null;
    }
}
