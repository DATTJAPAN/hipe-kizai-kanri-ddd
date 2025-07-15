<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Organizations;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAffiliatedOrganization
{
    public function affiliatedOrganization(): BelongsTo
    {
        if (method_exists($this, 'belongsTo')) {
            return $this->belongsTo(Organization::class, 'org_id');
        }

        throw new OrganizationAffiliationException();
    }
}
