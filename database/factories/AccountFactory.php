<?php

namespace Database\Factories;

use App\Enum\NetworksEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'person_id' => null,
            'network' => $this->faker->randomElement([NetworksEnum::FACEBOOK, NetworksEnum::TWITTER]),
            'handle' => $this->faker->userName
        ];
    }
}
