<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\WebService;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'webservice_id' => WebService::factory()->create()->id,
            'amount' => $this->faker->numberBetween(1, 10000),
            'type' => Transaction::$types[array_rand(Transaction::$types)]
        ];
    }
}
