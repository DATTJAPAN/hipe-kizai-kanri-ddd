<?php

declare(strict_types=1);

namespace App\Domains\Shared\Models;

use App\Domains\Organization\Organizations\HasOrganization;
use App\Domains\Organization\Users\HasOrganizationUserAsCreator;
use App\Support\Traits\Model\ModelExtension;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class OrganizationTag extends Model
{
    use HasOrganization;
    use HasOrganizationUserAsCreator;
    use HasPrefixedId;
    use ModelExtension;

    protected $table = 'organization_tags';

    protected $fillable = [
        'name',
        'code',
        'parent_tag_id',
        'org_id',
        'creator_org_user_id',
    ];

    protected $with = ['parentTag'];
    // ------------------------------------------------------------------------------
    // Model Custom Methods
    // ------------------------------------------------------------------------------

    public function parentTag(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_tag_id');
    }

    #[Scope]
    public function notPointingBackTo(Builder $query, int|string $parentIdentifier): Builder
    {
        $parentId = $parentIdentifier;

        if (is_string($parentIdentifier)) {
            $parentId = self::findByPrefixedId($parentIdentifier)->id;
        }

        // If prefixed_id doesn't exist, treat as if no parent found (exclude all pointing to null)
        if (is_null($parentId)) {
            return $query;
        }

        $query->where(function ($q) use ($parentId) {
            $q->whereNull('parent_tag_id')
                ->orWhere('parent_tag_id', '!=', $parentId);
        })->where('id', '!=', $parentId);

        return $query;
    }
}
