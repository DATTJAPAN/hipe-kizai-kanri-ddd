<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User as Authenticatable;

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

if (! function_exists('activeUser')) {
    function activeUser(): ?Authenticatable
    {
        $auth = auth()->guard(name: activeGuard());

        return $auth->check() ? $auth->user() : null;
    }
}
