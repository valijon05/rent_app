<?php

namespace MoonShine\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;

class MoonshineUserFactory extends Factory
{
    protected $model = MoonshineUser::class;

    /**
     * Define the model's default state.
     *
     * @return array{moonshine_user_role_id: mixed, name: string, email: string, password: string, remember_token: mixed}
     */
    public function definition(): array
    {
        return [
            'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
            'name' => str_replace("'", "", fake()->name()),
            'email' => fake()->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}
