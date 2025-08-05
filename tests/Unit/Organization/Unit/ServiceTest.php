<?php

declare(strict_types=1);

namespace Tests\Unit\Organization\Unit;

use App\Domains\Organization\Units\OrganizationUnitBuilder;
use App\Domains\Organization\Units\OrganizationUnitBuilderException;
use App\Domains\Organization\Units\OrganizationUnitException;
use App\Domains\Organization\Units\OrganizationUnitService;
use App\Domains\Organization\Units\OrganizationUnitType;
use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Domains\Organizations\Organization;
use App\Domains\Shared\Models\OrganizationUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrganizationUnitService $service;

    private Organization $org;

    private OrganizationUser $orgUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrganizationUnitService();
        $this->org = $this->generateRandomOrganization()['organization'];
        $this->orgUser = $this->org->users->first();
    }

    /**
     * @throws OrganizationUnitBuilderException
     * @throws OrganizationUnitException
     */
    public function test_create_parent_unit_successfully(): void
    {
        $builder = $this->createUnitBuilder(
            'Test Division',
            'DIV001',
            'Test Division Description',
            OrganizationUnitType::DIVISION,
            true
        );

        $unit = $this->service->createUsingBuilder($builder);

        $this->assertDatabaseHas('organization_units', [
            'id' => $unit->id,
            'name' => 'Test Division',
            'code' => 'DIV001',
            'description' => 'Test Division Description',
            'type' => OrganizationUnitType::DIVISION->name,
            'is_active' => true,
            'is_strict_hierarchy' => true,
            'org_id' => $this->org->id,
            'creator_org_user_id' => $this->orgUser->id,
            'parent_unit_id' => null,
        ]);

        $this->assertModelExists($unit);
        $this->assertEquals(OrganizationUnitType::DIVISION->defaultHierarchyLevel(), $unit->hierarchy);
    }

    /**
     * @throws OrganizationUnitBuilderException
     * @throws OrganizationUnitException
     */
    public function test_create_non_strict_child_team_unit_with_department_parent_successfully(): void
    {
        $parentUnit = $this->service->createUsingBuilder(
            $this->createUnitBuilder(
                'Test Department',
                'DEP001',
                'Department Description',
                OrganizationUnitType::DEPARTMENT
            )
        );

        $childUnit = $this->service->createUsingBuilder(
            $this->createUnitBuilder(
                'Test Team',
                'TEAM001',
                'Team Description',
                OrganizationUnitType::TEAM,
                false,
                $parentUnit
            )
        );

        $this->assertDatabaseHas('organization_units', [
            'id' => $childUnit->id,
            'name' => 'Test Team',
            'code' => 'TEAM001',
            'description' => 'Team Description',
            'type' => OrganizationUnitType::TEAM->name,
            'is_active' => true,
            'is_strict_hierarchy' => false,
            'org_id' => $this->org->id,
            'creator_org_user_id' => $this->orgUser->id,
            'parent_unit_id' => $parentUnit->id,
        ]);

        $this->assertModelExists($childUnit);
        $this->assertEquals(OrganizationUnitType::TEAM->defaultHierarchyLevel(), $childUnit->hierarchy);
    }

    public function test_create_strict_child_unit_fails_with_non_strict_parent(): void
    {
        $parentUnit = $this->service->createUsingBuilder(
            $this->createUnitBuilder(
                'Test Department',
                'DEP001',
                'Department Description',
                OrganizationUnitType::DEPARTMENT,
                false
            )
        );

        $this->expectException(OrganizationUnitBuilderException::class);

        $this->createUnitBuilder(
            'Test Unit',
            'UNIT001',
            'Unit Description',
            OrganizationUnitType::UNIT,
            true,
            $parentUnit
        )->build();
    }

    public function test_create_non_strict_child_unit_fails_with_strict_parent(): void
    {
        $parentUnit = $this->service->createUsingBuilder(
            $this->createUnitBuilder(
                'Test Department',
                'DEP001',
                'Department Description',
                OrganizationUnitType::DEPARTMENT,
                true
            )
        );

        $this->expectException(OrganizationUnitBuilderException::class);

        $this->createUnitBuilder(
            'Test Unit',
            'UNIT001',
            'Unit Description',
            OrganizationUnitType::UNIT,
            false,
            $parentUnit
        )->build();
    }

    public function test_create_strict_child_unit_fails_with_wrong_hierarchy(): void
    {
        $parentUnit = $this->service->createUsingBuilder(
            $this->createUnitBuilder(
                'Test Department',
                'DEP001',
                'Department Description',
                OrganizationUnitType::DEPARTMENT,
                true
            )
        );

        $this->expectException(OrganizationUnitBuilderException::class);

        $this->createUnitBuilder(
            'Test Unit',
            'UNIT001',
            'Unit Description',
            OrganizationUnitType::UNIT,
            true,
            $parentUnit
        )->build();
    }

    private function createUnitBuilder(
        string $name,
        string $code,
        string $description,
        OrganizationUnitType $type,
        bool $strictHierarchy = false,
        ?OrganizationUnit $parent = null
    ): OrganizationUnitBuilder {
        $builder = new OrganizationUnitBuilder();
        $builder->setName($name)
            ->setCode($code)
            ->setDescription($description)
            ->setType($type)
            ->setActive(true)
            ->setStrictHierarchy($strictHierarchy)
            ->setOrganization($this->org)
            ->setHeadOrgUser($this->orgUser)
            ->setCreatorOrgUser($this->orgUser);

        if ($parent) {
            $builder->setHasParentUnit($parent);
        }

        return $builder;
    }
}
