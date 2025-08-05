<?php

declare(strict_types=1);

namespace App\Domains\Organization\Tags;

use App\Domains\Shared\Models\OrganizationTag;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

class OrganizationTagFormData extends Data
{
    public function __construct(
        #[Required, Min(1), Max(100)]
        public string $name,

        #[Required, Min(1), Max(20)]
        public string $code,

        #[Sometimes, Nullable]
        public ?int $parent_tag_id,
    ) {}

    public static function rules(): array
    {
        $orgId = activeOrganizationUser()?->org_id;

        $prefixedId = request()->route('prefixedId');
        $currentId = null;
        if (null !== $prefixedId) {
            $currentId = OrganizationTag::where('prefixed_id', $prefixedId)->first()?->id;
        }

        return [
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique((new OrganizationTag)->getTable(), 'code')
                    ->where('org_id', $orgId)
                    ->ignore($currentId),
            ],
            'parent_tag_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($currentId) {
                    if (null !== $currentId && $value === $currentId) {
                        $fail('A tag cannot be assigned as its own parent.');
                    }

                    if (null !== $value && null !== $currentId) {
                        $childTag = OrganizationTag::find($value);
                        if ($childTag?->parent_tag_id === $currentId) {
                            $fail('Invalid parent: circular reference detected between tags.');
                        }
                    }
                },
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'code.unique' => 'The tag code has already been taken within your organization.',
        ];
    }
}
