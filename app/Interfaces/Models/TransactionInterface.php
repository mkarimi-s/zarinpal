<?php
declare(strict_types=1);

namespace App\Interfaces\Models;

interface TransactionInterface {
    /**
     * @param TransactionInterface $object Object.
     * @param WebServiceInterface $webService Web Service Interface.
     * @param integer $amount Amount.
     * @param int $type Type.
     *
     * @return self
     */
    public static function createObject(
        self $object,
        WebServiceInterface $webService,
        int $amount,
        int $type
    ): self;
}
