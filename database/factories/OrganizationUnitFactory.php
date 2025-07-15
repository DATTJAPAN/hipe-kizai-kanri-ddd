<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Organization\Units\OrganizationUnit;
use App\Domains\Organization\Units\OrganizationUnitType;
use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Domains\Organizations\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationUnitFactory extends Factory
{
    protected $model = OrganizationUnit::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(OrganizationUnitType::values());

        if (is_string($type)) {
            $type = OrganizationUnitType::tryFrom($type);
        }

        return [
            'name' => $type->name.' - '.str()->random(10),
            'description' => $this->faker->paragraph(),
            'type' => $type->name,
            'code' => mb_strtoupper(Str::random(6)),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'hierarchy' => $type->defaultHierarchyLevel(),
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
