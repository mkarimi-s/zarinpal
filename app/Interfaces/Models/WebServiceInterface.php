<?php
namespace App\Interfaces\Models;

interface WebServiceInterface {
    public static function createObject(self $object, int $name);
}
