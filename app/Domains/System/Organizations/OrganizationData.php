<?php

namespace App\Domains\System\Organizations;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Required;

class OrganizationData extends Data
{
    public function __construct(
        #[Required, Min(1), Max(100)]
        public string $name,

        #[Required, Email, Max(100)]
        public string $business_email,

        #[Required, Min(1), Max(100)]
        public string $domain,

        /** @var string[] */
        #[Max(5)]
        #[Regex('/^([a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/')]
        public array $alt_domains = []
    ) {}
}
