<?php

declare(strict_types=1);

namespace Tests\Unit\Organization\Tag;

use App\Domains\Organization\Tags\OrganizationTagException;
use App\Domains\Organization\Tags\OrganizationTagService;
use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Domains\Organizations\Organization;
use App\Domains\Shared\Models\OrganizationTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrganizationTagService $service;

    private Organization $org;

    private OrganizationUser $orgUser;

    private string $dbTable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrganizationTagService();
        $this->org = $this->generateRandomOrganization()['organization'];
        $this->orgUser = $this->org->users->first();
        $this->dbTable = (new OrganizationTag())->getTable();
    }

    public function test_create_tag_successfully(): void
    {
        $dummyData = [
            'name' => 'Test tag',
            'code' => 'TAG001',
            'org_id' => $this->org->id,
            'creator_org_user_id' => $this->orgUser->id,
        ];

        $model = $this->service->create($dummyData);

        $this->assertDatabaseHas($this->dbTable, $dummyData);
        $this->assertModelExists($model);
    }

    public function test_create_duplicate_tag_fails(): void
    {
        $dummyData = [
            'name' => 'Test tag',
            'code' => 'TAG001',
            'org_id' => $this->org->id,
            'creator_org_user_id' => $this->orgUser->id,
        ];

        $this->service->create($dummyData);
        $this->expectException(OrganizationTagException::class);
        $this->service->create($dummyData);
    }

    public function test_create_tag_with_invalid_data_fails(): void
    {
        $this->expectException(OrganizationTagException::class);
        $this->service->create([]);
    }

    public function test_update_tag_successfully(): void
    {
        $tag = $this->createDummyTag();

        $updateData = [
            'name' => 'Updated Tag',
            'code' => 'TAG002',
        ];

        $updated = $this->service->update($tag->id, $updateData);

        $this->assertDatabaseHas($this->dbTable, array_merge(
            ['id' => $tag->id],
            $updateData
        ));
        $this->assertEquals('Updated Tag', $updated->name);
        $this->assertEquals('TAG002', $updated->code);
    }

    public function test_update_non_existent_tag_fails(): void
    {
        $this->expectException(OrganizationTagException::class);
        $this->service->update(999, ['name' => 'Updated Tag']);
    }

    public function test_update_tag_with_empty_data_fails(): void
    {
        $tag = $this->createDummyTag();

        $this->expectException(OrganizationTagException::class);
        $this->service->update($tag->id, []);
    }

    public function test_delete_tag_successfully(): void
    {
        $tag = $this->createDummyTag();

        $result = $this->service->delete($tag->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing($this->dbTable, ['id' => $tag->id]);
    }

    public function test_delete_non_existent_tag_fails(): void
    {
        $this->expectException(OrganizationTagException::class);
        $this->service->delete(999);
    }

    public function test_create_tag_with_only_required_fields(): void
    {
        $dummyData = [
            'name' => 'Minimal Tag',
            'code' => 'MIN001',
            'org_id' => $this->org->id,
        ];

        $model = $this->service->create($dummyData);

        $this->assertDatabaseHas($this->dbTable, $dummyData);
        $this->assertModelExists($model);
    }

    public function test_update_tag_preserves_unchanged_fields(): void
    {
        $tag = $this->createDummyTag();
        $originalCode = $tag->code;

        $updated = $this->service->update($tag->id, [
            'name' => 'Updated Name Only',
        ]);

        $this->assertEquals('Updated Name Only', $updated->name);
        $this->assertEquals($originalCode, $updated->code);
    }

    private function createDummyTag(): OrganizationTag
    {
        return $this->service->create([
            'name' => 'Test tag',
            'code' => 'TAG001',
            'org_id' => $this->org->id,
            'creator_org_user_id' => $this->orgUser->id,
        ]);
    }
}
