<?php

declare(strict_types=1);

namespace Tests;

use App\Domains\Shared\Domains\Organizations\Organization;
use App\Domains\System\Users\SystemUser;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function generateRandomOrganization(): array
    {
        return [
            'system' => SystemUser::factory()->create(),
            'organization' => Organization::factory()->addRandomCreator()->create(),
        ];
    }
}
