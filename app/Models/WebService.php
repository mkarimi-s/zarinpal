<?php
declare(strict_types=1);

namespace App\Models;

use App\Interfaces\Models\WebServiceInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebService extends Model implements WebServiceInterface
{
    use HasFactory;

    protected $table = 'webservices';

    /**
     * @param WebServiceInterface $object   WebService
     * @param int $name Name
     *
     * @return WebServiceInterface
     */
    public static function createObject(WebServiceInterface $object, int $name): WebServiceInterface
    {
        $object->name = $name;
        $object->save();

        return $object;
    }
}
