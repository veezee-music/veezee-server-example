<?php

namespace App\Controllers\Admin;

use App\Controllers\SharedController;
use MongoDB;
use MongoDB\BSON\ObjectID;
use Soda\Core\Http\Controller;

class AdminController extends Controller
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
        return $this->render('admin.index', ['opr' => getOpR()]);
    }
}