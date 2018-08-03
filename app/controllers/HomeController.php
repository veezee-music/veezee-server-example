<?php

namespace App\Controllers;

use MongoDB\BSON\ObjectID;
use MongoDB\Driver\Exception\InvalidArgumentException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Soda\Core\Http\Controller;

class HomeController extends Controller
{
    private $dm;

    public function __construct()
    {
        parent::__construct();
        $this->dm = $this->getMongoDB()->getDM();
    }

    public function beforeActionExecution($action_name, $action_arguments)
    {
        parent::beforeActionExecution($action_name, $action_arguments);
    }

    protected function index()
    {
        die('working as expected.');
    }

    protected function insertData() {

        $prices = [
            'basic' => [
                '_id' => new ObjectID(),
                'name' => 'پایه',
                'amount' => 1000,
                'type' => 'basic'
            ],
            'premium' => [
                '_id' => new ObjectID(),
                'name' => 'ویژه',
                'amount' => 1000,
                'type' => 'premium'
            ],
            'carousel' => [
                '_id' => new ObjectID(),
                'name' => 'نمایش در اسلایدر',
                'amount' => 1000,
                'type' => 'carousel'
            ],
            'border' => [
                '_id' => new ObjectID(),
                'name' => 'تغییر رنگ اطراف تبلیغ',
                'amount' => 1000,
                'type' => 'border'
            ],
            'flashing' => [
                '_id' => new ObjectID(),
                'name' => 'تبلیغ چشمک زن',
                'amount' => 1000,
                'type' => 'flashing'
            ]
        ];

        $discounts = [
            [
                '_id' => new ObjectID(),
                'code' => 123456,
                'amount' => 50000,
                'type' => 'normal'
            ],
            [
                '_id' => new ObjectID(),
                'code' => 654321,
                'amount' => 10,
                'type' => 'percentage'
            ],
        ];


        $this->dm->selectCollection(PRICES)->insertOne($prices);
        $this->dm->selectCollection(DISCOUNTS)->insertMany($discounts);


        $this->echoNormal(["prices" => $prices , "discounts" => $discounts]);
    }



}