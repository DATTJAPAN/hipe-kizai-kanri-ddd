<?php

declare(strict_types=1);

namespace App\Domains\Organization\Organizations;

use App\Domains\Organization\Users\OrganizationUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasOrganization
{
    protected string $organizationForeignKey = 'org_id';

    public static function bootHasOrganization(): void
    {
        static::creating(function ($model) {
            $auth = auth()->guard(name: activeGuard());
            $authUser = $auth->check() ? $auth->user() : null;

            // Model will inherit the 'org_id' from the logged User
            if ($authUser instanceof OrganizationUser) {
                $model->{$this->organizationForeignKey} ??= $authUser->org_id;
            }
        });
    }

    public function affiliatedOrganization(): BelongsTo
    {
        if (method_exists($this, 'belongsTo')) {
            return $this->belongsTo(Organization::class, 'org_id');
        }

        throw new OrganizationAffiliationException();
    }
}
