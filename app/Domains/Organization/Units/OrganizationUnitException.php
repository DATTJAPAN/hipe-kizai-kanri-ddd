<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Core\Exceptions\HasExceptionFactory;
use Exception;

class OrganizationUnitException extends Exception
{
    use HasExceptionFactory;

    protected static string $defaultMessage = 'Organization Unit Exception';

    protected static string $createFailedMessage = 'Organization unit creation failed';

    protected static string $updateFailedMessage = 'Organization unit update failed';

    protected static string $deleteFailedMessage = 'Organization unit deletion failed';

    protected static string $notFoundMessage = 'Organization unit not found';

    protected static string $invalidDataMessage = 'Invalid organization unit data provided';

    protected static string $unexpectedErrorMessage = 'An unexpected error occurred with organization unit';

    protected static string $duplicateMessage = 'Organization unit already exists';

    public static function duplicate(string $name): self
    {
        return new self(
            self::formatMessage(static::$duplicateMessage, "Name: {$name}"),
            409
        );
    }
}
