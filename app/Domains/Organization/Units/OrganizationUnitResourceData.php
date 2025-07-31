<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Domains\Shared\Models\OrganizationUnit;
use Spatie\LaravelData\Attributes\LoadRelation;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class OrganizationUnitResourceData extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $prefixed_id,
        public string $name,
        public string $code,
        #[WithCast(EnumCast::class, OrganizationUnitType::class)]
        public OrganizationUnitType $type,

        #[LoadRelation]
        public ?OrganizationUnit $parent,
    ) {}
}
