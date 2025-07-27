<?php

declare(strict_types=1);

namespace App\Support\Providers;

use App\Domains\Organization\Networks\OrganizationNetwork;
use App\Domains\Organization\Tags\OrganizationTag;
use App\Domains\Organization\Units\OrganizationUnit;
use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Domains\Authorization\Permission;
use App\Domains\Shared\Domains\Authorization\Role;
use App\Domains\Shared\Models\Organization;
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
            'sys_user-' => SystemUser::class,
            'app_perm-' => Permission::class,
            'app-role-' => Role::class,
            'org-' => Organization::class,
            'org_user-' => OrganizationUser::class,
            'org_unit-' => OrganizationUnit::class,
            'org_tag-' => OrganizationTag::class,
            'org_network-' => OrganizationNetwork::class,
        ]);
    }
}
