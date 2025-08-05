<?php

declare(strict_types=1);

namespace App\Domains\Organization\Networks;

use Miken32\Validation\Network\Rules;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class OrganizationNetworkHostFormData extends Data
{
    public function __construct(
        #[Required, Min(1), Max(100)]
        public string $name,

        #[Required]
        public string $host_address
    ) {}

    public static function rules()
    {
        return [
            'host_address' => [
                new Rules\IpOrNet,
            ],
        ];
    }
}
