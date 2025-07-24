<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Organization\Organizations\Organization;
use App\Domains\Organization\Users\OrganizationUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class OrganizationUserFactory extends Factory
{
    protected $model = OrganizationUser::class;

    protected static ?string $password;

    public function definition(): array
    {
        $username = 'user_'.str()->random(10);

        return [
            'email' => fake()->unique()->safeEmail(),
            'username' => $username,
            'password' => self::$password ??= Hash::make('password'),
        ];
    }

    public function addForOrganization(Organization $org, string $emailPrefix = 'basic'): self
    {
        return $this->state(function (array $attributes) use ($org, $emailPrefix) {
            $orgId = $org->id;
            $email = $emailPrefix.'_'.str()->random(10).'@'.$org->domain;

            return [
                'email' => $email,
                'org_id' => $orgId,
            ];
        });
    }

    public function addForRandomOrganization(): self
    {
        return $this->addForOrganization(Organization::query()->inRandomOrder()->first());
    }
}
