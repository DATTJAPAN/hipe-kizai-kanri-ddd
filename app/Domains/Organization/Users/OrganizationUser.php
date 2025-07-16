<?php

declare(strict_types=1);

namespace App\Domains\Organization\Users;

use App\Support\Traits\Model\HasSettingsTrait;
use App\Support\Traits\Model\ModelExtension;
use Database\Factories\OrganizationUserFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class OrganizationUser extends Authenticatable
{
    use HasPrefixedId;
    use HasSettingsTrait;
    use ModelExtension;

    protected $table = 'organization_users';

    protected $fillable = [
        'email',
        'username',
        'password',
        'org_id',
    ];

    protected $setting_relation = 'userDefinedSettings';

    public function userDefinedSettings(): HasOne
    {
        return $this
            ->hasOne(OrganizationUserSetting::class, 'organization_user_id')
            ->withDefault(); // return empty if no data
    }

    protected static function newFactory()
    {
        return OrganizationUserFactory::new();
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
