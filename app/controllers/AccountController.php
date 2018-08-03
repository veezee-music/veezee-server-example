<?php

namespace App\Controllers;

use App\Utils\AuthHelper;
use MongoDB\BSON\ObjectID;
use Soda\Core\Http\Controller;

class AccountController extends Controller
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

    protected function login()
    {
        if(AuthHelper::isLoggedIn())
        {
            redirect('/account/info');
        }

        $admins = $this->dm->selectCollection(USERS)->find(['type' => 'admin'])->toArray();
        $message = null;
        if(count($admins) <= 0) {
            // no admins in the system
            $message = "No admins exist in the system. First user to sign up will have admin access!";
        }

        $email = $this->getRequest()->query->get('email');
        $redirect = $this->getRequest()->query->get('redirect', null);

        return $this->render('account.login', ['email' => $email, 'redirect' => $redirect, 'message' => $message]);
    }

    protected function register()
    {
        if(AuthHelper::isLoggedIn())
        {
            redirect('/account/info');
        }

        $admins = $this->dm->selectCollection(USERS)->find(['type' => 'admin'])->toArray();
        $message = null;
        if(count($admins) <= 0) {
            // no admins in the system
            $message = "No admins exist in the system. First user to sign up will have admin access!";
        }

        if(!USERS_API_ENABLED) {
            die('Error. New account registrations are NOT allowed. Change USERS_API_ENABLED flag value in the app.config to continue.');
        }

        return $this->render('account.register', ['message' => $message]);
    }

    protected function edit()
    {
        if(!AuthHelper::isLoggedIn())
        {
            redirect('/account/login?redirect=' . $_SERVER['REQUEST_URI']);
        }

        $user = (array) $this->dm->selectCollection(USERS)
            ->findOne(['_id' => new ObjectID($_SESSION['_id'])]);

        return $this->render('account.edit', ['user' => $user]);
    }

    protected function resetPassword()
    {
        return $this->render('account.reset-password');
    }

    protected function logOut()
    {
        AuthHelper::logOut();

        redirect(SITE_ADDRESS . '/account/login');
    }

    protected function info()
    {
        if(!AuthHelper::isLoggedIn())
        {
            redirect('/account/login?redirect=' . $_SERVER['REQUEST_URI']);
        }

        return $this->render('account.info');
    }
}