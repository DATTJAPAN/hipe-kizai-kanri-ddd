<?php

declare(strict_types=1);

namespace App\Domains\Shared\Models;

use App\Domains\Organization\Organizations\HasOrganization;
use App\Domains\Organization\Users\HasOrganizationUserAsCreator;
use App\Support\Traits\Model\ModelExtension;
use Illuminate\Database\Eloquent\Model;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class OrganizationNetworkHost extends Model
{
    use HasOrganization;
    use HasOrganizationUserAsCreator;
    use HasPrefixedId;
    use ModelExtension;

    protected $table = 'organization_network_hosts';

    protected $fillable = [
        'name',
        'host_address',
        'cidr',
        'broadcast_address',
        'org_id',
        'creator_org_user_id',
    ];

    // ------------------------------------------------------------------------------
    // Model Custom Methods
    // ------------------------------------------------------------------------------
}
