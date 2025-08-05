<?php

declare(strict_types=1);

namespace App\Domains\Organization\Locations;

use Spatie\LaravelData\Data;

class OrganizationLocationResourceData extends Data
{
    public function __construct(
        public ?int $id,
        public string $prefixed_id,
        public string $name,
        public ?string $description,
    ) {}
}
