<?php

declare(strict_types=1);

namespace App\Domains\Organization\Tags;

use App\Core\Exceptions\HasCrudExceptionFactory;
use Exception;

class OrganizationTagException extends Exception
{
    use HasCrudExceptionFactory;
}
