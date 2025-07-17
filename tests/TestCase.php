<?php

declare(strict_types=1);

namespace Tests;

use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Domains\Organizations\Organization;
use App\Domains\System\Users\SystemUser;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function generateRandomOrganization(): array
    {
        return [
            'system' => SystemUser::factory()->create(),
            'organization' => $org = Organization::factory()->addRandomCreator()->create([
                'name' => 'Datt Japan',
                'business_email' => 'datt@datt.co.jp',
                'domain' => 'datt.co.jp',
                'alt_domains' => ['datt.co.jp'],
            ]),
            'organization_user' => OrganizationUser::where('org_id', $org->id)->first(),
        ];
    }
}
