<?php

declare(strict_types=1);

namespace App\Domains\Organization\Tags;

use App\Domains\Shared\Models\OrganizationTag;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class OrganizationTagOptionData extends Data
{
    public function __construct(
        public string|int $id,
        public string $value,
        public string $displayName,
        public ?array $keywords,
    ) {}

    public static function fromCollection(Collection|EloquentCollection $data): Collection
    {
        return $data->map(function (OrganizationTag $item) {
            $displayName = sprintf('%s 「%s」', $item->name, $item->code);

            return new self(
                id: $item->id,
                value: $item->prefixed_id ?? (string) $item->id,
                displayName: $displayName,
                keywords: [$item->code, $item->name],
            );
        });
    }
}
