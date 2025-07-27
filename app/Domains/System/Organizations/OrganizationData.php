<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

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
        public array $alt_domains = [],

    ) {}

    public static function rules(): array
    {
        return [
            'business_email' => [
                'required',
                'email',
                'max:100',
                function ($attribute, $value, $fail) {
                    $query = \App\Domains\Shared\Models\Organization::where('business_email', $value);

                    // If we have an ID (update operation), exclude current record
                    if (request()->route('prefixedId')) {
                        $query->where('prefixed_id', '!=', request()->route('prefixedId'));
                    }

                    if ($query->exists()) {
                        $fail('This business email is already taken by another organization.');
                    }
                },
            ],
            'domain' => [
                'required',
                'string',
                'min:1',
                'max:100',
                function ($attribute, $value, $fail) {
                    $query = \App\Domains\Shared\Models\Organization::where('domain', $value);

                    // If we have an ID (update operation), exclude current record
                    if (request()->route('prefixedId')) {
                        $query->where('prefixed_id', '!=', request()->route('prefixedId'));
                    }

                    if ($query->exists()) {
                        $fail('This domain is already taken by another organization.');
                    }
                },
            ],
            'alt_domains' => ['array', 'max:5'],
            'alt_domains.*' => [
                'string',
                'regex:/^([a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
                'different:domain',
                'distinct',
                function ($attribute, $value, $fail) {
                    // Single query to check if alt domain conflicts with main domain OR alt domains of other organizations
                    $query = \App\Domains\Shared\Models\Organization::where(function ($q) use ($value) {
                        $q->where('domain', $value) // Check against main domains
                            ->orWhereJsonContains('alt_domains', $value); // Check against alt domains
                    });

                    // If we have an ID (update operation), exclude current record
                    if (request()->route('prefixedId')) {
                        $query->where('prefixed_id', '!=', request()->route('prefixedId'));
                    }

                    if ($query->exists()) {
                        $fail('This alternative domain is already used by another organization.');
                    }
                },
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'business_email.unique' => 'This business email is already taken by another organization.',
            'domain.unique' => 'This domain is already taken by another organization.',
            'alt_domains.*.different' => 'Alternative domains cannot be the same as the main domain.',
            'alt_domains.*.distinct' => 'Alternative domains must be unique.',
            'alt_domains.*.regex' => 'Each alternative domain must be a valid domain format.',
        ];
    }
}
