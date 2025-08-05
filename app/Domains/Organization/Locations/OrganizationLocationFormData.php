<?php

declare(strict_types=1);

namespace App\Domains\Organization\Locations;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

class OrganizationLocationFormData extends Data
{
    public function __construct(
        #[Required, Min(1), Max(100)]
        public string $name,
        #[Sometimes, Nullable, Max(300)]
        public ?string $description,
    ) {}
}
