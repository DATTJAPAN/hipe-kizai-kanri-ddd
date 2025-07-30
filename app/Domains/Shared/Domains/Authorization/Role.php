<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Authorization;

use App\Domains\Shared\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class Role extends Model
{
    use HasPrefixedId;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'hierarchy',
        'is_app_defaults',
        'is_app_default',
        'org_id',
        'org_type',
    ];

    // ------------------------------------------------------------------------------
    // Model Custom Methods
    // ------------------------------------------------------------------------------

    public static function generateDefaultRolesForOrganization(Organization $organization): void
    {
        $roles = config('role_permission.role.web');

        foreach ($roles as $role => $hierarchy) {
            (new self)::create([
                'name' => $role,
                'display_name' => $role,
                'guard_name' => 'web',
                'hierarchy' => $hierarchy,
                'is_app_default' => true, // flag as cannot be edited only the display; cannot be deleted either
                'org_id' => $organization->id,
                'org_type' => Organization::class,
            ]);
        }
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_has_permissions',
            'role_id',
            'permission_id'
        );
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name'] ?? config('auth.defaults.guard')),
            'model',
            'model_has_roles',
            'role_id',
            'model_id',
        );
    }
}
