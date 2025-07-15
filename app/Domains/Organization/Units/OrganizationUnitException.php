<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Core\Exceptions\HasExceptionFactory;
use Exception;

class OrganizationUnitException extends Exception
{
    use HasExceptionFactory;

    protected static string $defaultMessage = 'Role Exception';

    protected static string $createFailedMessage = 'Role creation failed';

    protected static string $updateFailedMessage = 'Role update failed';

    protected static string $deleteFailedMessage = 'Role deletion failed';

    protected static string $notFoundMessage = 'Role not found';

    protected static string $invalidDataMessage = 'Invalid role data provided';

    protected static string $unexpectedErrorMessage = 'An unexpected error occurred with role';

    protected static string $duplicateMessage = 'Role already exists';

    public static function duplicate(string $name): self
    {
        return new self(
            self::formatMessage(static::$duplicateMessage, "Name: {$name} "),
            409
        );
    }
}
