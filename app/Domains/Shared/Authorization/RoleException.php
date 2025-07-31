<?php

declare(strict_types=1);

namespace App\Domains\Shared\Authorization;

use App\Core\Exceptions\HasCrudExceptionFactory;
use Exception;

class RoleException extends Exception
{
    use HasCrudExceptionFactory;
}
