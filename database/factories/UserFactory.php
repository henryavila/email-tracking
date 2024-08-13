<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Database\Factories;

use HenryAvila\EmailTracking\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ];
    }
}
