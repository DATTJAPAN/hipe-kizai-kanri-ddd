<?php

declare(strict_types=1);

namespace App\Domains\System\Users;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SystemUserSetting extends Authenticatable
{
    public $incrementing = false;

    protected $table = 'system_user_settings';

    protected $primaryKey = 'system_user_id';

    protected $fillable = [
        'settings',
    ];

    public function settingsOwner(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, 'system_user_id');
    }

    protected function casts(): array
    {
        return [
            'settings' => AsArrayObject::class,
        ];
    }
}
