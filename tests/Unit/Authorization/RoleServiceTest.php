<?php

declare(strict_types=1);

namespace Tests\Unit\Authorization;

use App\Domains\Shared\Domains\Authorization\RoleBuilder;
use App\Domains\Shared\Domains\Authorization\RoleBuilderException;
use App\Domains\Shared\Domains\Authorization\RoleException;
use App\Domains\Shared\Domains\Authorization\RoleService;
use App\Domains\Shared\Domains\Organizations\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use RefreshDatabase;

    private RoleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoleService();
    }

    public function test_create_role_successfully(): void
    {
        $dummyData = [
            'name' => 'role_1',
            'display_name' => 'role_1_alias',
            'guard_name' => 'system',
        ];

        $roles = $this->service->create($dummyData);

        $this->assertDatabaseHas('roles', $dummyData);
        $this->assertModelExists($roles);
    }

    public function test_create_role_using_builder_successfully(): void
    {
        $builder = new RoleBuilder();

        $builder->setName('role_1')
            ->setAlias('role_1_alias')
            ->setGuardName('system');

        $roles = $this->service->createUsingBuilder($builder);

        $this->assertDatabaseHas('roles', $builder
            ->build()
            ->toArray()
        );

        $this->assertModelExists($roles);
    }

    /**
     * @throws RoleException
     * @throws RoleBuilderException
     */
    public function test_create_role_using_builder_with_organization_successfully(): void
    {
        $generated = $this->generateRandomOrganization();

        $builder = new RoleBuilder();

        $builder->setName('role_1')
            ->setAlias('role_1_alias')
            ->setGuardName('system')
            ->setForOrganizationOnly($generated['organization']);

        $role = $this->service->createUsingBuilder($builder);

        $this->assertDatabaseHas('roles', $builder
            ->build()
            ->toArray()
        );

        $this->assertModelExists($role);
    }

    /**
     * @throws RoleException
     */
    public function test_create_role_fails_with_duplicate_name(): void
    {
        $dummyData = [
            'name' => 'role_1',
            'display_name' => 'role_1_alias',
            'guard_name' => 'system',
        ];

        $this->service->create($dummyData);

        $this->expectException(RoleException::class);
        $this->expectExceptionMessage('Role creation failed');

        $builder = new RoleBuilder();

        $builder->setName('role_1')
            ->setAlias('role_1_alias')
            ->setGuardName('system');

        $this->service->createUsingBuilder($builder);
    }

    /**
     * @throws RoleException
     */
    public function test_create_role_fails_using_builder_with_empty_organization(): void
    {
        $builder = new RoleBuilder();

        $this->expectException(RoleBuilderException::class);
        $this->expectExceptionMessage('Organization must be existing.');

        $builder->setName('role_1')
            ->setAlias('role_1_alias')
            ->setGuardName('system')
            ->setForOrganizationOnly(new Organization());

        $role = $this->service->createUsingBuilder($builder);

        $this->assertDatabaseHas('roles', $builder->build()->toArray());
        $this->assertModelExists($role);
    }

    /**
     * @throws RoleException
     */
    public function test_update_role_by_model_successfully(): void
    {
        $builder = new RoleBuilder();

        $builder->setName('role_1')
            ->setAlias('role_1_alias')
            ->setGuardName('system');

        $roles = $this->service->createUsingBuilder($builder);

        $dummyData = ['name' => 'role_2', 'display_name' => 'role_2_alias'];
        $updated = $this->service->update($roles, $dummyData);

        $this->assertDatabaseHas('roles', $dummyData);
        $this->assertModelExists($updated);
    }

    /**
     * @throws RoleException
     */
    public function test_update_role_by_id_successfully(): void
    {
        $builder = new RoleBuilder();

        $builder->setName('role_1')
            ->setAlias('role_1_alias')
            ->setGuardName('system');

        $roles = $this->service->createUsingBuilder($builder);

        $dummyData = ['name' => 'role_2', 'display_name' => 'role_2_alias'];

        $updated = $this->service->update($roles->id, $dummyData);

        $this->assertDatabaseHas('roles', $dummyData);
        $this->assertModelExists($updated);
    }
}
