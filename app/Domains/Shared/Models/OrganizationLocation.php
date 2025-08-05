<?php

declare(strict_types=1);

namespace App\Domains\Shared\Models;

use App\Domains\Organization\Organizations\HasOrganization;
use App\Domains\Organization\Users\HasOrganizationUserAsCreator;
use App\Support\Traits\Model\ModelExtension;
use Illuminate\Database\Eloquent\Model;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class OrganizationLocation extends Model
{
    use HasOrganization;
    use HasOrganizationUserAsCreator;
    use HasPrefixedId;
    use ModelExtension;

    protected $table = 'organization_locations';

    protected $fillable = [
        'name',
        'description',
        'org_id',
        'creator_org_user_id',
    ];
}
