<?php

declare(strict_types=1);

namespace App\Domains\Organization\Tags;

use App\Domains\Organization\Organizations\HasOrganization;
use App\Domains\Organization\Users\HasOrganizationCreator;
use App\Support\Traits\Model\ModelExtension;
use Illuminate\Database\Eloquent\Model;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class OrganizationTag extends Model
{
    use HasOrganization;
    use HasOrganizationCreator;
    use HasPrefixedId;
    use ModelExtension;

    protected $table = 'organization_tags';

    protected $fillable = [
        'name',
        'code',
        'org_id',
        'creator_org_user_id',
    ];

    // ------------------------------------------------------------------------------
    // Model Custom Methods
    // ------------------------------------------------------------------------------

}
