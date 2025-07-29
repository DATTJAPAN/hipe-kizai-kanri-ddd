<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class OrganizationResourceData extends Data
{
    public function __construct(
        public string $name,
        public string $business_email,
        public string $domain,
        public array $alt_domains,
        public ?CarbonImmutable $deleted_at,
    ) {}
}
