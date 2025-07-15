<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

trait HasExceptionFactory
{
    public function __construct(
        string $message = '',
        ?int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            $message ?: (static::$defaultMessage ?? 'An error occurred'),
            $code,
            $previous
        );
    }

    public static function createFailed(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$createFailedMessage ?? 'Creation failed', $details)
        );
    }

    public static function updateFailed(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$updateFailedMessage ?? 'Update failed', $details),
            422
        );
    }

    public static function deleteFailed(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$deleteFailedMessage ?? 'Deletion failed', $details),
            422
        );
    }

    public static function notFound(int $id): self
    {
        return new self(
            (static::$notFoundMessage ?? 'Resource not found')." ID: {$id}",
            404
        );
    }

    public static function invalidData(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$invalidDataMessage ?? 'Invalid data provided', $details),
            422
        );
    }

    public static function unexpected(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$unexpectedErrorMessage ?? 'An unexpected error occurred', $details),
            500
        );
    }

    protected static function formatMessage(string $message, ?string $details = null): string
    {
        return $details ? "{$message} Details: {$details}" : $message;
    }
}
