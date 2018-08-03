<?php

namespace App\Services;

use Soda\Core\Database\MongoDBClient;

class BaseService
{
    public static function getDM()
    {
        $mongodb = new MongoDBClient();
        $dm = $mongodb->getDM();

        return $dm;
    }
}