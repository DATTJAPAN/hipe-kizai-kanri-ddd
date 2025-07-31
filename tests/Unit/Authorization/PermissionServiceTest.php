<?php

declare(strict_types=1);

namespace Tests\Unit\Authorization;

use App\Domains\Shared\Authorization\PermissionException;
use App\Domains\Shared\Authorization\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private PermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PermissionService();
    }

    public function test_create_permission_successfully(): void
    {
        $dummyData = [
            'name' => 'test:read_organization',
            'display_name' => 'test:read_organization',
        ];

        $created = $this->service->create($dummyData);

        $this->assertDatabaseHas('permissions', $dummyData);
        $this->assertModelExists($created);
    }

    /**
     * @throws PermissionException
     */
    public function test_update_permission_by_id_successfully(): void
    {
        $dummyData = [
            'name' => 'test:read_organization',
            'display_name' => 'test:read_organization',
        ];

        $model = $this->service->create($dummyData);

        $newDummyData = [
            'name' => 'test:create_organization',
            'display_name' => 'test:create_organization',
        ];

        $updated = $this->service->update($model->id, $newDummyData);

        $this->assertDatabaseHas('permissions', $newDummyData);
        $this->assertModelExists($updated);
    }

    /**
     * @throws PermissionException
     */
    public function test_update_permission_by_model_successfully(): void
    {
        $dummyData = [
            'name' => 'test:read_organization',
            'display_name' => 'test:read_organization',
        ];

        $model = $this->service->create($dummyData);

        $newDummyData = [
            'name' => 'test:create_organization',
            'display_name' => 'test:create_organization',
        ];

        $updated = $this->service->update($model, $newDummyData);

        $this->assertDatabaseHas('permissions', $newDummyData);
        $this->assertModelExists($updated);
    }

    /**
     * @throws PermissionException
     */
    public function test_create_permission_fails_with_duplicate_name(): void
    {
        $dummyData = [
            'name' => 'test:read_organization',
            'display_name' => 'test:read_organization',
        ];

        $this->service->create($dummyData);

        $this->expectException(PermissionException::class);
        $this->expectExceptionMessage('Permission creation failed');

        $this->service->create($dummyData);
    }
}
