<?php

declare(strict_types=1);

namespace App\Domains\Organization\Organizations;

use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Models\Organization;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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
                $model->{$model->organizationForeignKey} ??= $authUser->org_id;
            }
        });
    }

    public function parentOrganization(): BelongsTo
    {
        if (method_exists($this, 'belongsTo')) {
            return $this->belongsTo(Organization::class, $this->organizationForeignKey);
        }

        throw new OrganizationAffiliationException();
    }

    #[Scope]
    public function forOrganization(Builder $query, int|string $orgIdOrPrefixedId): Builder
    {
        $orgId = is_string($orgIdOrPrefixedId)
            ? Organization::findByPrefixedId($orgIdOrPrefixedId)?->id
            : $orgIdOrPrefixedId;

        if (is_null($orgId)) {
            return $query;
        }

        return $query->where($this->organizationForeignKey, $orgId);
    }
}
