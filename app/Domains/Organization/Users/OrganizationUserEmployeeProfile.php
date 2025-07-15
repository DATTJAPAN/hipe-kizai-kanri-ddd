<?php

declare(strict_types=1);

namespace App\Domains\Organization\Users;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OrganizationUserEmployeeProfile extends Authenticatable
{
    public $incrementing = false;

    protected $table = 'organization_user_employee_profiles';

    protected $primaryKey = 'organization_user_id';

    protected $fillable = [
        'employee_id',
        'work_name',
        'work_position',
    ];

    public function casts(): array
    {
        return [
            'settings' => AsArrayObject::class,
        ];
    }

    public function profileOwner(): BelongsTo
    {
        return $this->belongsTo(OrganizationUser::class, 'organization_user_id');
    }
}
