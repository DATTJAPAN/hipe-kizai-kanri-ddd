<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use App\Core\Exceptions\HasExceptionFactory;
use Exception;

class OrganizationException extends Exception
{
    use HasExceptionFactory;

    protected static string $defaultMessage = 'Organization Exception';

    protected static string $createFailedMessage = 'Organization creation failed';

    protected static string $updateFailedMessage = 'Organization update failed';

    protected static string $deleteFailedMessage = 'Organization deletion failed';

    protected static string $notFoundMessage = 'Organization not found';

    protected static string $invalidDataMessage = 'Invalid organization data provided';

    protected static string $unexpectedErrorMessage = 'An unexpected error occurred';
}
