<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Organization\Units\OrganizationUnit;
use App\Domains\Shared\Domains\Organizations\Organization;
use App\Domains\System\Users\SystemUser;
use Artisan;
use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command->warn('TestDatabaseSeeder skipped: not in local or testing environment.');

            return;
        }

        $this->command->info('Running TestDatabaseSeeder...');

        Artisan::call('background:sync-permissions');
        $this->seedSystemUsers();
        $this->seedOrganizations();
        $this->seedOrganizationDefaultDatas();
    }

    private function seedSystemUsers(): void
    {
        if (SystemUser::query()->exists()) {
            $this->command->info('Skipped seeding System Users (already exist).');

            return;
        }

        $this->command->info('Seeding System Users...');

        // Create a System User without a creator
        $systemUser = SystemUser::factory()->create();

        // Create a System User with the previous user as creator
        SystemUser::factory()
            ->for($systemUser, 'creator')
            ->create();

        // Create a System User with a random creator
        SystemUser::factory()
            ->addRandomCreator()
            ->create();

        $this->command->info('Finished seeding System Users.');
    }

    private function seedOrganizations(): void
    {
        if (Organization::query()->exists()) {
            $this->command->info('Skipped seeding Organizations (already exist).');

            return;
        }

        $this->command->info('Seeding Organizations...');
        // Example: create 3 Organizations
        Organization::factory()->addRandomCreator()->count(3)->create();

        $this->command->info('Finished seeding Organizations.');
    }

    private function seedOrganizationDefaultDatas(): void
    {
        Organization::all()->each(function (Organization $organization) {
            OrganizationUnit::factory()
                ->forOrganization($organization)
                ->count(3)
                ->create();
        });

    }
}
