<?php

namespace App\Domains\Organization\Tags;

use App\Domains\Shared\Domains\Organizations\HasAffiliatedOrganization;
use App\Support\Traits\Model\ModelExtension;
use Illuminate\Database\Eloquent\Model;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class OrganizationTags extends Model
{
    use HasPrefixedId;
    use ModelExtension;
    use HasAffiliatedOrganization;

    protected $table = 'organization_tags';

    protected $fillable = [
        'name',
        'description',
        'org_id',
        'creator_org_user_id'
    ];

    // ------------------------------------------------------------------------------
    // Model Custom Methods
    // ------------------------------------------------------------------------------


}
