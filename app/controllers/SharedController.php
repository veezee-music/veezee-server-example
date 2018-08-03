<?php

namespace App\Controllers;

use MongoDB;
use MongoDB\BSON\ObjectID;
use Soda\Core\Http\Controller;
use MongoDB\BSON\Regex;

class SharedController extends Controller
{
    private $dm;

    public static $questionTypes = [
        [
            'type' => '3_leveled',
            'typeDisplayName' => '۳ سطحی',
            'optionsCount' => 3,
            'selectsCount' => 1
        ],
        [
            'type' => '4_leveled',
            'typeDisplayName' => '۴ سطحی',
            'optionsCount' => 4,
            'selectsCount' => 1
        ],
        [
            'type' => '5_leveled',
            'typeDisplayName' => '۵ سطحی',
            'optionsCount' => 5,
            'selectsCount' => 1
        ],
        [
            'type' => '2_options_1_select_max',
            'typeDisplayName' => '۲ انتخابی ۱ جوابه',
            'optionsCount' => 2,
            'selectsCount' => 1
        ],
        [
            'type' => '3_options_1_select_max',
            'typeDisplayName' => '۳ انتخابی ۱ جوابه',
            'optionsCount' => 3,
            'selectsCount' => 1
        ],
        [
            'type' => '4_options_1_select_max',
            'typeDisplayName' => '۴ انتخابی ۱ جوابه',
            'optionsCount' => 4,
            'selectsCount' => 1
        ],
        [
            'type' => '5_options_1_select_max',
            'typeDisplayName' => '۵ انتخابی ۱ جوابه',
            'optionsCount' => 5,
            'selectsCount' => 1
        ]
    ];

    public static $questionStatuses = [
        [
            'status' => 'active',
            'statusDisplayName' => 'فعال'
        ],
        [
            'status' => 'disabled',
            'statusDisplayName' => 'غیر فعال'
        ],
        [
            'status' => 'voting',
            'statusDisplayName' => 'رای گیری'
        ],
        [
            'status' => 'pending',
            'statusDisplayName' => 'بررسی'
        ],
        [
            'status' => 'accepted',
            'statusDisplayName' => 'قبول شده'
        ],
        [
            'status' => 'rejected',
            'statusDisplayName' => 'رد شده'
        ]
    ];

    public static $states = [
        'تهران',
        'البرز',
        'آذربایجان شرقی',
        'آذربایجان غربی',
        'اردبیل',
        'اصفهان',
        'ایلام',
        'بوشهر',
        'چهارمحال و بختیاری',
        'خراسان جنوبی',
        'خراسان رضوی',
        'خراسان شمالی',
        'خوزستان',
        'زنجان',
        'سمنان',
        'سیستان و بلوچستان',
        'فارس',
        'قزوین',
        'قم',
        'کردستان',
        'کرمان',
        'کرمانشاه',
        'کهگیلویه و بویراحمد',
        'گلستان',
        'گیلان',
        'لرستان',
        'مازندران',
        'مرکزی',
        'هرمزگان',
        'همدان',
        'یزد'
    ];

    public static $universityTypes = [
        'دولتی',
        'آزاد',
        'غیر انتفاعی',
        'پیام نور',
        'علمی کاربردی'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->dm = $this->getMongoDB()->getDM();
    }

    public function beforeActionExecution($action_name, $action_arguments)
    {
        parent::beforeActionExecution($action_name, $action_arguments);
    }

    protected function getQuestionTypes()
    {
        return $this->echoNormal(SharedController::$questionTypes);
    }

    protected function getQuestionStatuses()
    {
        return $this->echoNormal(SharedController::$questionStatuses);
    }

    protected function getStates()
    {
        return $this->echoNormal(SharedController::$states);
    }

    protected function search()
    {
        $search = $this->getRequest()->query->get('search');

        $universities = [];
        $professors = [];

        if(mb_stripos($search, 'دانشگاه', 0, 'UTF-8') !== false)
        {
            // most likely a search for a university
            goto AFTER_PROFESSORS_SEARCH;
        }

        // professors search
        $query = ['active' => true];
        if($search != '' && $search != null)
        {
            $query['$or'] = [
                ['firstName' => new Regex("$search", '')],
                ['lastName' => new Regex("$search", '')],
                ['mainUniversity' => new Regex("$search", '')],
            ];
        }

        $professors = $this->dm->selectCollection('Professors')->find($query, [
            'sort' => ['_id' => -1],
            'projection' => [
                'firstName' => 1,
                'lastName' => 1,
                'gender' => 1,
                'majors' => 1,
                'mainUniversity' => 1,
                'reactions' => 1
            ],
        ])->toArray();

        AFTER_PROFESSORS_SEARCH:

        // universities search
        $query = ['active' => true];
        if($search != '' && $search != null)
        {
            $query['$or'] = [
                ['title' => new Regex("$search", '')],
                ['state' => new Regex("$search", '')],
                ['city' => new Regex("$search", '')],
            ];
        }

        $universities = $this->dm->selectCollection('Universities')->find($query, [
            'sort' => ['_id' => -1],
            'projection' => [
                'title' => 1,
                'type' => 1,
                'majors' => 1,
                'state' => 1,
                'city' => 1,
                'image' => 1,
                'reactions' => 1
            ],
        ])->toArray();

        $result = array_merge($universities, $professors);

        foreach($result as &$i)
        {
            $i['show'] = false;

            if(isset($i['title']))
            {
                // university
                $i['resultType'] = 'university';
            }
            elseif(isset($i['firstName']))
            {
                $i['resultType'] = 'professor';
            }

            $i['reactions'] = isset($i['reactions']) ? array_slice($i['reactions'], -40) : [];
            $i['reviewsCount'] = isset($i['reactions']) ? count($i['reactions']) : 0;

            unset($i);
        }

        return $this->echoNormal($result);
    }
}