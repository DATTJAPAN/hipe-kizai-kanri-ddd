<?php

declare(strict_types=1);

namespace App\Domains\Organization\Networks;

use App\Core\Exceptions\HasCrudExceptionFactory;
use Exception;

class OrganizationNetworkHostException extends Exception
{
    use HasCrudExceptionFactory;
}
