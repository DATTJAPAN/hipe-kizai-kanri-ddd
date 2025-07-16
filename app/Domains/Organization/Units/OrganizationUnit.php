<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Domains\Organizations\HasOrganization;
use App\Support\Traits\Model\ModelExtension;
use Database\Factories\OrganizationUnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class OrganizationUnit extends Model
{
    use HasFactory;
    use HasOrganization;
    use HasPrefixedId;
    use ModelExtension;

    protected $table = 'organization_units';

    protected $fillable = [
        'name',
        'code',
        'description',
        // ----
        'type',
        'hierarchy',
        'is_strict_hierarchy',
        'is_active',
        // ----
        'parent_unit_id',
        'head_org_user_id',
        'org_id',
        'creator_org_user_id',
    ];

    // ------------------------------------------------------------------------------
    // Model Relations Methods
    // ------------------------------------------------------------------------------

    public function parentAffiliation(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_affiliation_id');
    }

    public function childrenAffiliations(): HasMany
    {
        return $this->hasMany(self::class, 'parent_affiliation_id');
    }

    public function headOrganizationUser(): BelongsTo
    {
        return $this->belongsTo(OrganizationUser::class, 'head_org_user_id');
    }

    public function organizationUserMembers(): BelongsToMany
    {
        return $this->belongsToMany(
            OrganizationUser::class,
            'organization_unit_members',
            'org_unit_id',
            'org_user_id'
        )
            ->withTimestamps();
    }

    // ------------------------------------------------------------------------------
    // Model Custom Methods
    // ------------------------------------------------------------------------------
    public function isStrictMode(): bool
    {
        return $this->is_strict_hierarchy;
    }

    protected static function newFactory()
    {
        return OrganizationUnitFactory::new();
    }

    // ------------------------------------------------------------------------------
    // Model Configure Methods
    // ------------------------------------------------------------------------------

    protected function casts(): array
    {
        return [
            'type' => OrganizationUnitType::class,
        ];
    }
}
