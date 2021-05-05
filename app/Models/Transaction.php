<?php

namespace App\Models;

use App\Interfaces\Models\TransactionInterface;
use App\Interfaces\Models\WebServiceInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model implements TransactionInterface
{
    use HasFactory;

    const TYPE_WEB = 0;
    const TYPE_MOBILE = 1;
    const TYPE_POS = 2;

    /**
     * @var array
     */
    public static array $types = [
        self::TYPE_WEB,
        self::TYPE_MOBILE,
        self::TYPE_POS
    ];

    /**
     * @param TransactionInterface $object New Transaction Object.
     * @param WebServiceInterface $webService Web Service Object.
     * @param int $amount Amount.
     * @param int $type Type.
     * @return TransactionInterface
     */
    public static function createObject(
        TransactionInterface $object,
        WebServiceInterface $webService,
        int $amount,
        int $type
    ): TransactionInterface
    {
        $object->webservice_id = $webService->id;
        $object->amount = $amount;
        $object->type = $type;
        $object->save();

        return $object;
    }
}
