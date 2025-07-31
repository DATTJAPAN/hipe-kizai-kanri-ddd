<?php

declare(strict_types=1);

namespace App\Domains\Shared\Models;

use App\Domains\Organization\Organizations\HasOrganization;
use App\Domains\Organization\Units\OrganizationUnitType;
use App\Domains\Organization\Users\HasOrganizationUserAsCreator;
use App\Domains\Organization\Users\OrganizationUser;
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
    use HasOrganizationUserAsCreator;
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

    protected $with = ['parentUnit'];

    // ------------------------------------------------------------------------------
    // Model Relations Methods
    // ------------------------------------------------------------------------------

    public function parentUnit(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_unit_id');
    }

    public function childrenAffiliations(): HasMany
    {
        return $this->hasMany(self::class, 'parent_unit_id');
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

    // ------------------------------------------------------------------------------
    // Model Configuration
    // ------------------------------------------------------------------------------
    protected static function newFactory()
    {
        return OrganizationUnitFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(static function (OrganizationUnit $unit) {
            if ($unit->type && $unit->type instanceof OrganizationUnitType) {
                $unit->hierarchy = $unit->type->defaultHierarchyLevel();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'type' => OrganizationUnitType::class,
        ];
    }
}
