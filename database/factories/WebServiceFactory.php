<?php

namespace Database\Factories;

use App\Models\WebService;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WebService::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomDigit
        ];
    }
}
