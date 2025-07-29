<?php

declare(strict_types=1);

namespace App\Domains\Shared\Repositories;

use Exception;

class BaseRepositoryException extends Exception
{
    public static function emptyData(): self
    {
        return new self(class_basename(self::class.': Cannot try to create with an empty data.'));
    }
}
