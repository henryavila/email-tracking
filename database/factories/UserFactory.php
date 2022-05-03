<?php

namespace AppsInteligentes\EmailTracking\Database\Factories;

use AppsInteligentes\EmailTracking\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'     => $this->faker->name,
            'email'    => $this->faker->email,
            'password' => $this->faker->password,
        ];
    }
}

