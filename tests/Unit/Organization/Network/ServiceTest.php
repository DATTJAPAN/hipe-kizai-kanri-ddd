<?php

declare(strict_types=1);

namespace Tests\Unit\Organization\Network;

use App\Domains\Organization\Networks\OrganizationNetwork;
use App\Domains\Organization\Networks\OrganizationNetworkException;
use App\Domains\Organization\Networks\OrganizationNetworkService;
use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Domains\Organizations\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrganizationNetworkService $service;

    private Organization $org;

    private OrganizationUser $orgUser;

    private string $dbTable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrganizationNetworkService();
        $this->org = $this->generateRandomOrganization()['organization'];
        $this->orgUser = $this->org->users->first();
        $this->dbTable = (new OrganizationNetwork)->getTable();
    }

    public function test_create_network_successfully(): void
    {
        $networkData = [
            'name' => 'Test Network',
            'network_address' => '192.168.1.0',
            'network_address_long' => ip2long('192.168.1.0'),
            'cidr' => '24',
            'broadcast' => '192.168.1.255',
            'org_id' => $this->org->id,
            'creator_org_user_id' => $this->orgUser->id,
        ];

        $model = $this->service->create($networkData);

        $this->assertDatabaseHas($this->dbTable, $networkData);
        $this->assertModelExists($model);
    }

    public function test_create_duplicate_network_fails(): void
    {
        $networkData = [
            'name' => 'Test Network',
            'network_address' => '192.168.1.0',
            'network_address_long' => ip2long('192.168.1.0'),
            'cidr' => '24',
            'broadcast' => '192.168.1.255',
            'org_id' => $this->org->id,
            'creator_org_user_id' => $this->orgUser->id,
        ];

        $this->service->create($networkData);
        $this->expectException(OrganizationNetworkException::class);
        $this->service->create($networkData);
    }

    public function test_create_network_with_invalid_data_fails(): void
    {
        $this->expectException(OrganizationNetworkException::class);
        $this->service->create([]);
    }

    public function test_update_network_successfully(): void
    {
        $network = $this->createDummyNetwork();

        $updateData = [
            'name' => 'Updated Network',
            'network_address' => '192.168.2.0',
            'network_address_long' => ip2long('192.168.2.0'),
            'cidr' => '24',
            'broadcast' => '192.168.2.255',
        ];

        $updated = $this->service->update($network->id, $updateData);

        $this->assertDatabaseHas($this->dbTable, array_merge(
            ['id' => $network->id],
            $updateData
        ));
        $this->assertEquals('Updated Network', $updated->name);
        $this->assertEquals('192.168.2.0', $updated->network_address);
    }

    public function test_update_non_existent_network_fails(): void
    {
        $this->expectException(OrganizationNetworkException::class);
        $this->service->update(999, ['name' => 'Updated Network']);
    }

    public function test_update_network_with_empty_data_fails(): void
    {
        $network = $this->createDummyNetwork();

        $this->expectException(OrganizationNetworkException::class);
        $this->service->update($network->id, []);
    }

    public function test_delete_network_successfully(): void
    {
        $network = $this->createDummyNetwork();

        $result = $this->service->delete($network->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing($this->dbTable, ['id' => $network->id]);
    }

    public function test_delete_non_existent_network_fails(): void
    {
        $this->expectException(OrganizationNetworkException::class);
        $this->service->delete(999);
    }

    public function test_create_network_with_only_required_fields(): void
    {
        $networkData = [
            'name' => 'Minimal Network',
            'network_address' => '192.168.1.0',
            'network_address_long' => ip2long('192.168.1.0'),
            'cidr' => '24',
            'broadcast' => '192.168.1.255',
            'org_id' => $this->org->id,
            'creator_org_user_id' => $this->orgUser->id,
        ];

        $model = $this->service->create($networkData);

        $this->assertDatabaseHas($this->dbTable, $networkData);
        $this->assertModelExists($model);
    }

    public function test_update_network_preserves_unchanged_fields(): void
    {
        $network = $this->createDummyNetwork();
        $originalAddress = $network->network_address;

        $updated = $this->service->update($network->id, [
            'name' => 'Updated Name Only',
        ]);

        $this->assertEquals('Updated Name Only', $updated->name);
        $this->assertEquals($originalAddress, $updated->network_address);
    }

    private function createDummyNetwork(): OrganizationNetwork
    {
        return $this->service->create([
            'name' => 'Network 1',
            'network_address' => '192.168.1.0',
            'network_address_long' => ip2long('192.168.1.0'),
            'cidr' => '24',
            'broadcast' => '192.168.1.255',
            'org_id' => $this->org->id,
            'creator_org_user_id' => $this->orgUser->id,
        ]);
    }
}
