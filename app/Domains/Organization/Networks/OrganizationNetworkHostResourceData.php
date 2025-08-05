<?php

declare(strict_types=1);

namespace App\Domains\Organization\Networks;

use Spatie\LaravelData\Data;

class OrganizationNetworkHostResourceData extends Data
{
    public function __construct(
        public ?int $id,
        public string $prefixed_id,
        public string $name,
        public string $host_address,
    ) {}
}
