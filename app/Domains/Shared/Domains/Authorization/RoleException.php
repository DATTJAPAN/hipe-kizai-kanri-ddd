<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Authorization;

use App\Core\Exceptions\HasCrudExceptionFactory;
use Exception;

class RoleException extends Exception
{
    use HasCrudExceptionFactory;
}
