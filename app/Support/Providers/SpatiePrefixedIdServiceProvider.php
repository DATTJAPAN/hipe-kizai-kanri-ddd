<?php

declare(strict_types=1);

namespace App\Support\Providers;

use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Authorization\Permission;
use App\Domains\Shared\Authorization\Role;
use App\Domains\Shared\Models\Organization;
use App\Domains\Shared\Models\OrganizationLocation;
use App\Domains\Shared\Models\OrganizationNetworkHost;
use App\Domains\Shared\Models\OrganizationTag;
use App\Domains\Shared\Models\OrganizationUnit;
use App\Domains\System\Users\SystemUser;
use Illuminate\Support\ServiceProvider;
use Spatie\PrefixedIds\PrefixedIds;

class SpatiePrefixedIdServiceProvider extends ServiceProvider
{
    private static int $idLength = 10;

    public function boot(): void
    {
        PrefixedIds::generateUniqueIdUsing(static function () {
            return mb_substr(bin2hex(random_bytes(self::$idLength / 2)), 0, self::$idLength);
        });

        // Ref: https://github.com/spatie/laravel-prefixed-ids
        PrefixedIds::registerModels([
            'sys_user_' => SystemUser::class,
            'app_permission_' => Permission::class,
            'app_role_' => Role::class,
            'org_' => Organization::class,
            'org_user_' => OrganizationUser::class,
            'org_unit_' => OrganizationUnit::class,
            'org_tag_' => OrganizationTag::class,
            'org_location_' => OrganizationLocation::class,
            'org_network_host_' => OrganizationNetworkHost::class,
        ]);
    }
}
