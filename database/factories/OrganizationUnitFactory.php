<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Organization\Units\OrganizationUnitType;
use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Models\Organization;
use App\Domains\Shared\Models\OrganizationUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationUnitFactory extends Factory
{
    protected $model = OrganizationUnit::class;

    public function definition(): array
    {
        $type = OrganizationUnitType::DEPARTMENT;

        return [
            'name' => $type->name.' - '.str()->random(10),
            'description' => $this->faker->paragraph(),
            'type' => $type->name,
            'code' => mb_strtoupper(Str::random(6)),
            'is_active' => $this->faker->boolean(100), // 80% chance of being active
            'hierarchy' => $type->defaultHierarchyLevel(),
            'is_strict_hierarchy' => true,
            // set via state
            'head_org_user_id' => null,
            'org_id' => null,
            'parent_unit_id' => null,
        ];
    }

    public function forOrganization(Organization $org): static
    {
        $creator = OrganizationUser::query()
            ->inRandomOrder()
            ->where('org_id', $org->id)
            ->first();

        return $this->state(fn (array $attributes) => [
            'org_id' => $org->id,
            'creator_org_user_id' => $creator->id,
            'head_org_user_id' => $creator->id,
        ]);
    }

    public function forRandomOrganization(): static
    {
        return $this->forOrganization(Organization::query()->inRandomOrder()->first());
    }
}
