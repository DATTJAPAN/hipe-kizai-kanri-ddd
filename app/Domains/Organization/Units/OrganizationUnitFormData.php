<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class OrganizationUnitFormData extends Data
{
    public function __construct(
        #[Required, Min(1), Max(100)]
        public string $name,

        #[Required, Min(1), Max(10)]
        public string $code,

        #[Sometimes, Nullable, Max(300)]
        public ?string $description,

        #[WithCast(EnumCast::class, OrganizationUnitType::class)]
        public OrganizationUnitType $type,

        #[Sometimes, Nullable]
        public ?int $parent_unit_id,
    ) {}
}
