<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use App\Core\Exceptions\HasExceptionFactory;
use Exception;

class OrganizationException extends Exception
{
    use HasExceptionFactory;
}
