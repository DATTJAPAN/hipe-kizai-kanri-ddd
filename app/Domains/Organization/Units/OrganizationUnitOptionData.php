<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Domains\Shared\Models\OrganizationUnit;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class OrganizationUnitOptionData extends Data
{
    public function __construct(
        public string|int $id,
        public string $value,
        public string $displayName,
        public ?array $keywords,
        #[WithCast(EnumCast::class, OrganizationUnitType::class)]
        public OrganizationUnitType $type
    ) {}

    public static function fromCollection(Collection|EloquentCollection $data): Collection
    {
        return $data->map(function (OrganizationUnit $item) {
            $unitType = $item->type instanceof OrganizationUnitType ? $item->type->name : $item->type;
            $displayName = sprintf(
                '%s 「%s」',
                $item->name,
                str($unitType)->isAscii()
                    ? str($unitType)->lower()->ucfirst()->toString()
                    : $unitType
            );

            return new self(
                id: $item->id,
                value: $item->prefixed_id ?? (string) $item->id,
                displayName: $displayName,
                keywords: [$item->code, $item->name],
                type: $item->type,
            );
        });
    }
}
