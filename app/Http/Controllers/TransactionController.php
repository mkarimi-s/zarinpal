<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\WebService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * @param TransactionRequest $request Transaction Request.
     *
     * @return TransactionResource
     */
    public function store(TransactionRequest $request): TransactionResource
    {
        $webService = WebService::find($request->get('webservice_id'));
        $amount = $request->get('amount');
        $amount = $request->input('type') === Transaction::TYPE_POS ? $amount : $amount * 10;
        DB::beginTransaction();
        try {
            $transaction = Transaction::createObject(
                new Transaction,
                $webService,
                $amount,
                $request->input('type')
            );
            DB::commit();
        }catch (\Exception $exception) {
            Log::critical($exception->getMessage());
            DB::rollBack();
        }

        return new TransactionResource($transaction);
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $transactions = Transaction::query()->selectRaw(
            "SUM(CASE WHEN amount > 0 AND amount < '5000' THEN 1 ELSE 0 END) AS a,
            SUM(CASE WHEN amount > '5000' AND amount < '10000' THEN 1 ELSE 0 END) AS b,
            SUM(CASE WHEN amount > '10000' AND amount < '100000' THEN 1 ELSE 0 END) AS c,
            SUM(CASE WHEN amount > '100000' THEN 1 ELSE 0 END) AS d"
        )->groupBy('type')->orderBy('type', 'asc')->get();

        list(
            $count_of_type_a,
            $count_of_type_b,
            $count_of_type_c,
            $count_of_type_d,
            $web_count,
            $mobileCount,
            $pos_count
        ) = $this->manipulateTransactionTypeCount($transactions);

        return response()->json([
            'transactions' => [
                '0to5000' => $count_of_type_a,
                '5000to10000' => $count_of_type_b,
                '10000to100000' => $count_of_type_c,
                '100000toup' => $count_of_type_d,
            ],
            'summary' => [
                'amount' => ($web_count + $pos_count + $mobileCount),
                'web_count' => $web_count,
                'pos_count' => $pos_count,
                'mobile_count' => $mobileCount,
            ],
        ]);
    }

    /**
     * @param Collection|array $transactions
     * @return array
     */
    private function manipulateTransactionTypeCount(Collection|array $transactions): array
    {
        $count_of_type_a = $transactions->first()->a +
            $transactions->offsetGet(1)->a +
            $transactions->offsetGet(2)->a;

        $count_of_type_b = $transactions->first()->b +
            $transactions->offsetGet(1)->b +
            $transactions->offsetGet(2)->b;

        $count_of_type_c = $transactions->first()->c +
            $transactions->offsetGet(1)->c +
            $transactions->offsetGet(2)->c;

        $count_of_type_d = $transactions->first()->d +
            $transactions->offsetGet(1)->d +
            $transactions->offsetGet(2)->d;

        $web_count = $transactions->first()->a +
            $transactions->first()->b +
            $transactions->first()->c +
            $transactions->first()->d;

        $mobileCount = $transactions->offsetGet(1)->a +
            $transactions->offsetGet(1)->b +
            $transactions->offsetGet(1)->c +
            $transactions->offsetGet(1)->d;

        $pos_count = $transactions->offsetGet(2)->a +
            $transactions->offsetGet(2)->b +
            $transactions->offsetGet(2)->c +
            $transactions->offsetGet(2)->d;
        return array(
            $count_of_type_a,
            $count_of_type_b,
            $count_of_type_c,
            $count_of_type_d,
            $web_count,
            $mobileCount,
            $pos_count
        );
    }
}
