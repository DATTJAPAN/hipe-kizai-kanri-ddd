<?php

declare(strict_types=1);

namespace App\Core\Authentication;

use InvalidArgumentException;

class InvalidCredentialsException extends InvalidArgumentException
{
    private const DEFAULT_MESSAGE = 'Invalid credentials. please try again.';

    private const HTTP_STATUS_CODE = 401;

    public function __construct(?string $message = self::DEFAULT_MESSAGE)
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return self::HTTP_STATUS_CODE;
    }
}
