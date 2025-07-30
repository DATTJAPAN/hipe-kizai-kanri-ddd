<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Shared\Models\Organization;
use App\Domains\System\Users\SystemUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $email = $this->faker->companyEmail;
        $domain = explode('@', $email)[1];
        $companyName = explode('.', $domain)[0];

        return [
            'name' => $companyName,
            'business_email' => $email,
            'domain' => $domain,
            'alt_domains' => [$domain],
        ];
    }

    public function addRandomCreator(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'creator_sys_user_id' => SystemUser::query()->inRandomOrder()->value('id'),
            ];
        });
    }
}
