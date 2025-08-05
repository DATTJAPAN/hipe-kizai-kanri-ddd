<?php

declare(strict_types=1);

namespace App\Domains\Organization\Tags;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;

class OrganizationTagResourceData extends Data
{
    public function __construct(
        public ?int $id,
        public string $prefixed_id,
        public string $name,
        public string $code,
        #[MapOutputName('parent_tag')]
        public ?self $parentTag,
    ) {}
}
