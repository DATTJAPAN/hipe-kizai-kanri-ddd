<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Authorization;

use App\Core\Exceptions\HasCrudExceptionFactory;
use Exception;

class RoleBuilderException extends Exception
{
    use HasCrudExceptionFactory;

    protected static string $defaultMessage = 'Role Builder Exception';
}
