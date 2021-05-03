<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamTest extends TestCase
{
    /**
     * @return void
     */
    public function test_pos_transaction_request()
    {
        $response = $this->postJson(
            '/api/transaction/pos', 
            [
                'amount' => 10000 // rial
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'transaction_id',
                'created_at',
            ]);
    }

    /**
     * @return void
     */
    public function test_web_transaction_request()
    {
        $response = $this->postJson(
            '/api/transaction/web', 
            [
                'amount' => 1000 // toman
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'transaction_id',
            ]);
    }
    /**
     * @return void
     */
    public function test_mobile_transaction_request()
    {
        $response = $this->postJson(
            '/api/transaction/mobile', 
            [
                'amount' => 1000 // toman
            ]
        );

        $response
            ->assertStatus(201);
    }
    /**
     * @return void
     */
    public function test_get_last_month_statistics()
    {
        $response = $this->getJson('/api/transactions');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'transactions' => [ // sum of amount beetween these ranges
                    '0to5000',
                    '5000to10000',
                    '10000to100000',
                    '100000toup',
                ],
                'summary' => [
                    'amount',
                    'web_count',
                    'pos_count',
                    'mobile_count',
                ],
            ]);
    }
}
