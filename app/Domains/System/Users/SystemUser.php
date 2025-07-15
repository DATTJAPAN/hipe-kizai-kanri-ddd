<?php

declare(strict_types=1);

namespace App\Domains\System\Users;

use App\Domains\Organization\Organizations\Organization;
use App\Support\Traits\Model\HasSettingsTrait;
use Database\Factories\SystemUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class SystemUser extends Authenticatable
{
    use HasFactory;
    use HasPrefixedId;
    use HasSettingsTrait;

    protected $table = 'system_users';

    protected $fillable = [
        'email',
        'username',
        'password',
        'creator_id',
    ];

    protected $setting_relation = 'userDefinedSettings';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(self::class, 'creator_id', 'id');
    }

    public function createdSystemUsers(): HasMany
    {
        return $this->hasMany(self::class, 'creator_id', 'id');
    }

    public function createdOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function userDefinedSettings(): HasOne
    {
        return $this
            ->hasOne(SystemUserSetting::class, 'system_user_id')
            ->withDefault(); // return empty if no data
    }

    /**
     * Scopes
     */
    public function scopeNoCreator($q)
    {
        return $q->whereNull('creator_id');
    }

    protected static function newFactory()
    {
        return SystemUserFactory::new();
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
