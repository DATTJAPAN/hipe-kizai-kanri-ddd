<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Organizations;

use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Domains\Authorization\Concerns\HasPermissions;
use App\Domains\Shared\Domains\Authorization\Contract\HasPermissionContract;
use App\Domains\Shared\Domains\Authorization\Role;
use App\Support\Traits\Model\ModelExtension;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class Organization extends Model implements HasPermissionContract
{
    use HasFactory;
    use HasPermissions;
    use HasPrefixedId;
    use ModelExtension;

    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'domain',
        'business_email',
    ];

    protected $casts = [
        'alt_domains' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(OrganizationUser::class, 'org_id');
    }

    // ------------------------------------------------------------------------------
    // Model Configure Methods
    // ------------------------------------------------------------------------------
    protected static function booted(): void
    {
        static::created(static function ($organization) {
            // Create a default user:
            OrganizationUser::create([
                'org_id' => $organization->id,
                'email' => sprintf('super@%s',
                    str($organization->business_email)
                        ->lower()
                        ->afterLast('@')
                        ->trim()
                        ->toString()
                ),
                'username' => 'super',
                'password' => 'super',
            ]);

            // Generate default roles
            Role::generateDefaultRolesForOrganization($organization);
        });
    }

    protected static function newFactory()
    {
        return OrganizationFactory::new();
    }

    // ------------------------------------------------------------------------------
    // Model Custom Methods for Traits
    // ------------------------------------------------------------------------------
    protected function definePermissionList(): array
    {
        return $this->makeMultiDomainPermission(
            $this,
            resource: 'organization', actions: ['create', 'read', 'update', 'delete']
        );
    }
}
