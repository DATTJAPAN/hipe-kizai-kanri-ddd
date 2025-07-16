<?php

namespace App\Domains\Organization\Networks;

use App\Domains\Organization\Users\HasOrganizationCreator;
use App\Domains\Shared\Domains\Organizations\HasOrganization;
use App\Support\Traits\Model\ModelExtension;
use Illuminate\Database\Eloquent\Model;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class OrganizationNetwork extends Model
{
    use HasOrganization;
    use HasOrganizationCreator;
    use HasPrefixedId;
    use ModelExtension;

    protected $table = 'organization_networks';

    protected $fillable = [
        'name',
        'network_address',
        'network_address_long',
        'cidr',
        'broadcast',
        'org_id',
        'creator_org_user_id'
    ];

    // ------------------------------------------------------------------------------
    // Model Custom Methods
    // ------------------------------------------------------------------------------


}
