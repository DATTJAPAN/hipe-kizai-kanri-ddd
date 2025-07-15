<?php

declare(strict_types=1);

namespace App\Core\Authentication;

use Exception;

class TooManyAuthAttemptException extends Exception
{
    private const DEFAULT_MESSAGE = 'Too many login attempts. Please try again later.';

    private const MINUTES_THRESHOLD = 60;

    private const HTTP_STATUS_CODE = 429;

    public function __construct(
        private readonly ?int $secondsUntilAvailable = null,
        string $message = self::DEFAULT_MESSAGE
    ) {
        parent::__construct($message);
    }

    public static function make(?int $seconds): self
    {
        if (null === $seconds) {
            return new self();
        }

        $message = self::formatMessage($seconds);

        return new self($seconds, $message);
    }

    public function getSecondsUntilAvailable(): ?int
    {
        return $this->secondsUntilAvailable;
    }

    public function getStatusCode(): int
    {
        return self::HTTP_STATUS_CODE;
    }

    private static function formatMessage(int $seconds): string
    {
        if ($seconds >= self::MINUTES_THRESHOLD) {
            $minutes = ceil($seconds / 60);

            return "Too many login attempts. Please try again in {$minutes} minutes.";
        }

        return "Too many login attempts. Please try again in {$seconds} seconds.";
    }
}
