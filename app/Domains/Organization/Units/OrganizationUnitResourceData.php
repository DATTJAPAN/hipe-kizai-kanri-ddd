<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class OrganizationUnitResourceData extends Data
{
    public function __construct(
        public ?int $id,
        public string $prefixed_id,
        public string $name,
        public string $code,
        public ?string $description,
        #[WithCast(EnumCast::class, OrganizationUnitType::class)]
        public OrganizationUnitType $type,
        #[MapOutputName('parent_unit')]
        public ?self $parentUnit,
    ) {}
}
