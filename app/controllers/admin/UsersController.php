<?php

namespace App\Controllers\Admin;

use Soda\Core\Http\Controller;

class UsersController extends Controller
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
        $list = $this->dm->selectCollection(USERS)->find([])->toArray();

        return $this->render('admin.users.index', ['opr' => getOpR(), 'list' => $list]);
    }
}