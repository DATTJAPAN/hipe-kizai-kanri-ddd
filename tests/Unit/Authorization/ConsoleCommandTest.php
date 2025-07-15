<?php

declare(strict_types=1);

namespace Tests\Unit\Authorization;

use App\Console\Commands\Background\SyncPermissionsCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsoleCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_permissions_command_can_be_executed(): void
    {
        $this->artisan(SyncPermissionsCommand::class)
            ->assertSuccessful();
    }
}
