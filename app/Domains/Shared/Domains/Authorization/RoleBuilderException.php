<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Authorization;

use App\Core\Exceptions\HasExceptionFactory;
use Exception;

class RoleBuilderException extends Exception
{
    use HasExceptionFactory;

    protected static string $defaultMessage = 'Role Builder Exception';
}
