<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\System\Users\SystemUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class SystemUserFactory extends Factory
{
    protected $model = SystemUser::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'username' => $this->faker->unique()->username(),
            'password' => self::$password ??= Hash::make('password'),
        ];
    }

    public function addRandomCreator(): self
    {
        return $this->afterCreating(function (SystemUser $systemUser) {
            $systemUser->update(['creator_id' => SystemUser::query()->inRandomOrder()->value('id')]);
        });
    }
}
