<?php

declare(strict_types=1);

namespace App\Domains\Organization\Users;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OrganizationUserPersonalProfile extends Authenticatable
{
    public $incrementing = false;

    protected $table = 'organization_user_personal_profiles';

    protected $primaryKey = 'organization_user_id';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'email',

        // -----
        'contact_mobile',
        'address',
        // ----
    ];

    public function profileOwner(): BelongsTo
    {
        return $this->belongsTo(OrganizationUser::class, 'organization_user_id');
    }
}
