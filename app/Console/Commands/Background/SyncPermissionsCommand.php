<?php

declare(strict_types=1);

namespace App\Console\Commands\Background;

use App\Domains\Shared\Domains\Authorization\Permission;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'background:sync-permissions';

    protected $description = 'Ensure permissions are synced with the database and the classes that contains them';

    public function handle(): int
    {
        // Check if first time
        if (Permission::all()->isEmpty()) {
            $this->info('First time, start registering permissions.');

            return $this->runFirstTime();
        }

        $this->info('Not first time, syncing permissions now.');

        return $this->runNotFirstTime();
    }

    private function runFirstTime(): int
    {
        try {
            (new Permission())->generateDefaultPermissions();
            $this->info('successfully register permissions.');

            return CommandAlias::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Something went wrong during registering of permission.');

            return CommandAlias::FAILURE;
        }
    }

    private function runNotFirstTime(): int
    {
        try {
            (new Permission())->syncDefaultPermissions();
            $this->info('successfully synced permissions.');

            return CommandAlias::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Something went wrong during syncing of permission.');

            return CommandAlias::FAILURE;
        }
    }
}
