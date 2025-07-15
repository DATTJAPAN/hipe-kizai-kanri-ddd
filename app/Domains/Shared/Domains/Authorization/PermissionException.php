<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Authorization;

use App\Core\Exceptions\HasExceptionFactory;
use Exception;

class PermissionException extends Exception
{
    use HasExceptionFactory;

    protected static string $defaultMessage = 'Permission Exception';

    protected static string $createFailedMessage = 'Permission creation failed';

    protected static string $updateFailedMessage = 'Permission update failed';

    protected static string $deleteFailedMessage = 'Permission deletion failed';

    protected static string $notFoundMessage = 'Permission not found';

    protected static string $invalidDataMessage = 'Invalid role data provided';

    protected static string $unexpectedErrorMessage = 'An unexpected error occurred with role';

    protected static string $duplicateMessage = 'Permission already exists';

    public static function duplicate(string $name): self
    {
        return new self(
            self::formatMessage(static::$duplicateMessage, "Name: {$name} "),
            409
        );
    }
}
