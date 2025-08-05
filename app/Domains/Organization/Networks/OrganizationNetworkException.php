<?php

declare(strict_types=1);

namespace App\Domains\Organization\Networks;

use App\Core\Exceptions\HasCrudExceptionFactory;
use Exception;

class OrganizationNetworkException extends Exception
{
    use HasCrudExceptionFactory;

    protected static string $defaultMessage = 'Organization Tag Exception';

    protected static string $createFailedMessage = 'Organization tag creation failed';

    protected static string $updateFailedMessage = 'Organization tag update failed';

    protected static string $deleteFailedMessage = 'Organization tag deletion failed';

    protected static string $notFoundMessage = 'Organization tag not found';

    protected static string $invalidDataMessage = 'Invalid organization tag data provided';

    protected static string $unexpectedErrorMessage = 'An unexpected error occurred with organization tag';

    protected static string $duplicateMessage = 'Organization tag already exists';

    public static function duplicate(string $name): self
    {
        return new self(
            self::formatMessage(static::$duplicateMessage, "Name: {$name}"),
            409
        );
    }
}
