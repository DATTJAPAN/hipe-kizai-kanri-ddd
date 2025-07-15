<?php

declare(strict_types=1);

namespace Tests\Unit\Organization;

use App\Domains\System\Organizations\OrganizationException;
use App\Domains\System\Organizations\OrganizationService;
use App\Domains\System\Users\SystemUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test class to handle unit tests for CRUD operations on organizations
 * using the `OrganizationService` class methods.
 *
 * This class focuses on testing the service layer directly and not HTTP interactions.
 */
class ServiceCrudTest extends TestCase
{
    use RefreshDatabase;

    private OrganizationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrganizationService();
    }

    /**
     * Test all related to "Create" Methods
     */
    public function test_create_organization_successfully(): void
    {
        $systemUser = SystemUser::factory()->create();

        $dummyData = [
            'name' => 'test',
            'domain' => 'test@gmail.com',
            'business_email' => 'test@gmail.com',
            'creator_id' => $systemUser->id,
        ];

        $organization = $this->service->create($dummyData);

        $this->assertDatabaseHas('organizations', $dummyData);
        $this->assertModelExists($organization);
    }

    public function test_create_organization_fails_with_empty_data(): void
    {
        $this->expectException(OrganizationException::class);
        $this->expectExceptionCode(422);

        $this->service->create([]);
    }

    public function test_create_organization_fails_with_invalid_data(): void
    {
        $this->expectException(OrganizationException::class);

        $invalidData = [
            'name' => '',
            'domain' => 'invalid-email',
            'business_email' => '',
        ];

        $this->service->create($invalidData);
    }

    /**
     * @throws OrganizationException
     */
    public function test_create_organization_fails_with_duplicate_domain(): void
    {
        $systemUser = SystemUser::factory()->create();

        $orgData = [
            'name' => 'First Org',
            'domain' => 'test@gmail.com',
            'business_email' => 'test@gmail.com',
            'creator_id' => $systemUser->id,
        ];

        // Create first organization
        $this->service->create($orgData);

        $this->expectException(OrganizationException::class);

        // Try to create second organization with same domain
        $duplicateData = [
            'name' => 'Second Org',
            'domain' => 'test@gmail.com',
            'business_email' => 'test@gmail.com',
            'creator_id' => $systemUser->id,
        ];

        $this->service->create($duplicateData);
    }

    public function test_create_organization_fails_with_missing_required_fields(): void
    {
        $this->expectException(OrganizationException::class);

        $incompleteData = [
            'name' => 'test',
        ];

        $this->service->create($incompleteData);
    }

    public function test_create_organization_fails_with_invalid_creator_id(): void
    {
        $this->expectException(OrganizationException::class);

        $invalidCreatorData = [
            'name' => 'Test Org',
            'domain' => 'test@gmail.com',
            'business_email' => 'test@gmail.com',
            'creator_id' => 999999, // Non-existent creator ID
        ];

        $this->service->create($invalidCreatorData);
    }

    /**
     * @throws OrganizationException
     */
    public function test_create_organization_creates_default_user(): void
    {
        $systemUser = SystemUser::factory()->create();

        $orgData = [
            'name' => 'Test Org',
            'domain' => 'test@gmail.com',
            'business_email' => 'test@gmail.com',
            'creator_id' => $systemUser->id,
        ];

        $organization = $this->service->create($orgData);

        $this->assertDatabaseHas('organization_users', [
            'org_id' => $organization->id,
            'email' => 'super@gmail.com',
            'username' => 'super',
        ]);
    }

    /**
     * Test all related to "Update" Methods
     */
    public function test_update_organization_successfully(): void
    {
        $systemUser = SystemUser::factory()->create();
        $organization = $this->service->create([
            'name' => 'Original Name',
            'domain' => 'test@gmail.com',
            'business_email' => 'test@gmail.com',
            'creator_id' => $systemUser->id,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'business_email' => 'updated@gmail.com',
        ];

        $updatedOrganization = $this->service->update($organization->id, $updateData);

        $this->assertEquals('Updated Name', $updatedOrganization->name);
        $this->assertEquals('updated@gmail.com', $updatedOrganization->business_email);
        $this->assertDatabaseHas('organizations', $updateData);
    }

    public function test_update_organization_fails_with_empty_data(): void
    {
        $this->expectException(OrganizationException::class);
        $this->service->update(1, []);
    }

    public function test_update_organization_fails_with_non_existent_id(): void
    {
        $this->expectException(OrganizationException::class);

        $this->service->update(999, ['name' => 'New Name']);
    }

    /**
     * Test all related to "Delete" Methods
     */
    public function test_delete_organization_successfully(): void
    {
        $systemUser = SystemUser::factory()->create();
        $organization = $this->service->create([
            'name' => 'To Be Deleted',
            'domain' => 'delete@gmail.com',
            'business_email' => 'delete@gmail.com',
            'creator_id' => $systemUser->id,
        ]);

        $result = $this->service->delete($organization->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('organizations', ['id' => $organization->id]);
    }

    public function test_delete_organization_fails_with_non_existent_id(): void
    {
        $this->expectException(OrganizationException::class);

        $this->service->delete(999);
    }
}
