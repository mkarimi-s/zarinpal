<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\WebService;
use Tests\TestCase;

class ExamTest extends TestCase
{
    /**
     * @return void
     */
    public function test_pos_transaction_request()
    {
        $webService = WebService::factory()->create();
        $amount = 10000;
        $response = $this->postJson(
            '/api/transaction/pos',
            [
                'amount' => $amount, // rial
                'webservice_id' => $webService->id
            ]
        );

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'webservice_id',
                    'amount',
                    'type',
                    'created_at',
                    'updated_at',
                    'webservice'
                ]
            ]);
        $this->assertEquals($response->getOriginalContent()->webservice_id, $webService->id);
        $this->assertEquals($response->getOriginalContent()->amount, $amount);
        $this->assertEquals($response->getOriginalContent()->type, Transaction::TYPE_POS);

    }

    /**
     * @return void
     */
    public function test_web_transaction_request()
    {
        $webService = WebService::factory()->create();
        $amount = 1000;
        $response = $this->postJson(
            '/api/transaction/web',
            [
                'amount' => $amount, // toman
                'webservice_id' => $webService->id
            ]
        )->assertCreated();

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'webservice_id',
                    'amount',
                    'type',
                    'created_at',
                    'updated_at',
                    'webservice'
                ]
            ]);
        $this->assertEquals($response->getOriginalContent()->webservice_id, $webService->id);
        $this->assertEquals($response->getOriginalContent()->amount, $amount * 10); // should be stored in rial
        $this->assertEquals($response->getOriginalContent()->type, Transaction::TYPE_WEB);
    }
    /**
     * @return void
     */
    public function test_mobile_transaction_request()
    {
        $webService = WebService::factory()->create();
        $amount = 1000;
        $response = $this->postJson(
            '/api/transaction/mobile',
            [
                'amount' => $amount, // toman
                'webservice_id' => $webService->id
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'webservice_id',
                    'amount',
                    'type',
                    'created_at',
                    'updated_at',
                    'webservice'
                ]
            ]);
        $this->assertEquals($response->getOriginalContent()->webservice_id, $webService->id);
        $this->assertEquals($response->getOriginalContent()->amount, $amount * 10); // should be stored in rial
        $this->assertEquals($response->getOriginalContent()->type, Transaction::TYPE_MOBILE);
    }
    /**
     * @return void
     */
    public function test_get_last_month_statistics()
    {
        Transaction::query()->truncate();
        $transactions = Transaction::factory()->count(100)->create();
        $transactionsWithAmountLowerThan5000 = $transactions
            ->where('amount', '>' , 0)
            ->where('amount', '<' , 5000)->count();
        $transactionsWithAmountLowerThan10000 = $transactions
            ->where('amount', '>' , 5000)
            ->where('amount', '<' , 10000)->count();
        $transactionsWithAmountLowerThan100000 = $transactions
            ->where('amount', '>' , 10000)
            ->where('amount', '<' , 100000)->count();
        $transactionsWithAmountGreaterThan100000 = $transactions
            ->where('amount', '>' , 100000)
            ->count();


        $response = $this->getJson('/api/transactions');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'transactions' => [ // sum of amount between these ranges
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
        $this->assertEquals(
            $response->getOriginalContent()["transactions"]["0to5000"], $transactionsWithAmountLowerThan5000
        );
        $this->assertEquals(
            $response->getOriginalContent()["transactions"]["5000to10000"], $transactionsWithAmountLowerThan10000
        );
        $this->assertEquals(
            $response->getOriginalContent()["transactions"]["10000to100000"], $transactionsWithAmountLowerThan100000
        );
        $this->assertEquals(
            $response->getOriginalContent()["transactions"]["100000toup"], $transactionsWithAmountGreaterThan100000
        );
        $this->assertEquals(
            $response->getOriginalContent()["summary"]["web_count"] +
            $response->getOriginalContent()["summary"]["pos_count"] +
            $response->getOriginalContent()["summary"]["mobile_count"],
            100
        );
    }
}
