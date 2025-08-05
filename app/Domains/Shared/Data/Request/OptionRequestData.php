<?php

declare(strict_types=1);

namespace App\Domains\Shared\Data\Request;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Data;

class OptionRequestData extends Data
{
    public function __construct(
        #[BooleanType]
        public bool $onlyTrashed = false,
        #[BooleanType]
        public bool $withTrashed = false,
        public ?array $exclude = null,
        public ?array $scopes = null,
    ) {
        if ($this->onlyTrashed && $this->withTrashed) {
            $this->withTrashed = false;
        }
    }

    public function isShowingTrashed(): bool
    {
        return $this->onlyTrashed || $this->withTrashed;
    }
}
