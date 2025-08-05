<?php

declare(strict_types=1);

namespace App\Domains\Shared\Data\Request;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Data;

class DatatableRequestData extends Data
{
    public function __construct(
        #[BooleanType]
        public bool $onlyTrashed = false,

        #[BooleanType]
        public bool $withTrashed = false,

        public ?array $exclude = null,

        public ?array $additionalData = null,
    ) {
        // Ensure mutual exclusivity
        if ($this->onlyTrashed && $this->withTrashed) {
            $this->withTrashed = false;
        }
    }

    /**
     * Get additional data excluding the main properties
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData ?? [];
    }

    /**
     * Check if showing trashed items in any form
     */
    public function isShowingTrashed(): bool
    {
        return $this->onlyTrashed || $this->withTrashed;
    }
}
