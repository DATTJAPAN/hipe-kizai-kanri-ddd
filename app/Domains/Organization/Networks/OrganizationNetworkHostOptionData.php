<?php

declare(strict_types=1);

namespace App\Domains\Organization\Networks;

use App\Domains\Shared\Data\Response\BaseOptionData;
use App\Domains\Shared\Models\OrganizationNetworkHost;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class OrganizationNetworkHostOptionData extends BaseOptionData
{
    public static function fromCollection(Collection|EloquentCollection $data): Collection
    {
        return $data->map(function (OrganizationNetworkHost $item) {
            return new self(
                id: $item->id,
                value: $item->prefixed_id ?? (string) $item->id,
                displayName: $item->name,
                keywords: [$item->name],
            );
        });
    }
}
