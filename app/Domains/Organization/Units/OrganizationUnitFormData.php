<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Domains\Shared\Models\OrganizationUnit;
use Illuminate\Validation\Rule;
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

        #[Required, Min(1), Max(20)]
        public string $code,

        #[Sometimes, Nullable, Max(300)]
        public ?string $description,

        #[WithCast(EnumCast::class, OrganizationUnitType::class)]
        public OrganizationUnitType $type,

        #[Sometimes, Nullable]
        public ?int $parent_unit_id,

    ) {}

    public static function rules(): array
    {
        $orgId = activeOrganizationUser()?->org_id;

        $prefixedId = request()->route('prefixedId');
        $currentId = null;
        if (null !== $prefixedId) {
            $currentId = OrganizationUnit::where('prefixed_id', $prefixedId)->first()?->id;
        }

        return [
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique((new OrganizationUnit)->getTable(), 'code')
                    ->where('org_id', $orgId)
                    ->ignore($currentId),
            ],
            'parent_unit_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($currentId) {
                    if (null !== $currentId && $value === $currentId) {
                        $fail('A unit cannot be assigned as its own parent.');
                    }
                },
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'code.unique' => 'The unit code has already been taken within your organization.',
        ];
    }
}
