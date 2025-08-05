<?php

declare(strict_types=1);

namespace App\Domains\Shared\Data\Response;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

abstract class BaseOptionData extends Data
{
    public function __construct(
        public string|int $id,
        public string $value,
        public string $displayName,
        public ?array $keywords,
    ) {}

    /**
     * Override
     */
    abstract public static function fromCollection(Collection|EloquentCollection $data): Collection;
}
