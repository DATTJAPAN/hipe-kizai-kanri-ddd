<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Throwable;

trait HasCrudExceptionFactory
{
    protected string $targetClass;

    private static string $factoryDefaultMessage = 'An error occurred';

    private static string $factoryCreateFailedMessage = 'Creation failed';

    private static string $factoryUpdateFailedMessage = 'Update failed';

    private static string $factoryDeleteFailedMessage = 'Deletion failed';

    private static string $factoryNotFoundMessage = 'Resource not found';

    private static string $factoryInvalidDataMessage = 'Invalid data provided';

    private static string $factoryUnexpectedErrorMessage = 'An unexpected error occurred';

    private static string $factoryDuplicateMessage = 'Resource already exists';

    public function __construct(
        string $message = '',
        ?int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->targetClass = static::getDefaultTargetClass();

        parent::__construct(
            $message ?: ($this->targetClass.': '.static::$factoryDefaultMessage),
            $code,
            $previous
        );
    }

    public static function createFailed(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$factoryCreateFailedMessage, $details),
            422
        );
    }

    public static function updateFailed(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$factoryUpdateFailedMessage, $details),
            422
        );
    }

    public static function deleteFailed(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$factoryDeleteFailedMessage, $details),
            422
        );
    }

    public static function notFound(string|int $id): self
    {
        return new self(
            static::getDefaultTargetClass().': '.static::$factoryNotFoundMessage." (ID: {$id})",
            404
        );
    }

    public static function invalidData(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$factoryInvalidDataMessage, $details),
            422
        );
    }

    public static function unexpected(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$factoryUnexpectedErrorMessage, $details),
            500
        );
    }

    public static function duplicate(?string $details = null): self
    {
        return new self(
            self::formatMessage(static::$factoryDuplicateMessage, $details),
            409
        );
    }

    protected static function getDefaultTargetClass(): string
    {
        // Extract class name without namespace
        return class_basename(static::class);
    }

    protected static function formatMessage(string $message, ?string $details = null): string
    {
        $prefix = static::getDefaultTargetClass();
        $baseMessage = "{$prefix}: {$message}";

        return $details ? "{$baseMessage}. Details: {$details}" : $baseMessage;
    }
}
