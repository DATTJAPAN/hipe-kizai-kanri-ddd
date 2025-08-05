<?php

declare(strict_types=1);

namespace App\Domains\Shared\Models;

use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Authorization\Concerns\HasPermissions;
use App\Domains\Shared\Authorization\Contract\HasPermissionContract;
use App\Domains\Shared\Authorization\Role;
use App\Domains\System\Users\HasSystemAsCreator;
use App\Support\Traits\Model\ModelExtension;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class Organization extends Model implements HasPermissionContract
{
    use HasFactory;
    use HasPermissions;
    use HasPrefixedId;
    use HasSystemAsCreator;
    use ModelExtension;
    use SoftDeletes;

    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'domain',
        'business_email',
        'alt_domains',
    ];

    protected $casts = [
        'alt_domains' => 'array',
    ];

    protected static function newFactory()
    {
        return OrganizationFactory::new();
    }

    // ------------------------------------------------------------------------------
    // Model Configure Methods
    // ------------------------------------------------------------------------------
    protected static function booted(): void
    {
        static::created(static function (self $self) {
            // Generate super user:
            OrganizationUser::generateDefaultSuperUserForOrganization($self);
            // Generate default roles
            Role::generateDefaultRolesForOrganization($self);
        });
    }

    // ------------------------------------------------------------------------------
    // Model Custom Methods for Traits
    // ------------------------------------------------------------------------------
    protected function definePermissionList(): array
    {
        return $this->makeMultiDomainPermission(
            $this,
            resource: 'organization',
            actions: ['create', 'read', 'update', 'delete']
        );
    }
}
