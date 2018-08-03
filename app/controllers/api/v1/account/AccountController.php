<?php

namespace App\Controllers\API\V1\Account;

use App\Utils\AuthHelper;
use App\Utils\JWTHelper;
use App\Utils\PasswordV1;
use App\Utils\VeezeeUserAccessHelper;
use Couchbase\UserSettings;
use Firebase\JWT\JWT;
use MongoDB;
use MongoDB\BSON\ObjectID;
use Soda\Core\Http\Controller;
use MongoDB\BSON\Regex;

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

    public function register()
    {
        $input = getJSONInput();

        if(!isset($input['name']) || $input['name'] == null) {
            return $this->echoError(['error' => 'Name field can not be empty.']);
        } else if(!preg_match('/^[a-zA-Z0-9]+$/', $input['name'])) {
            return $this->echoError(['error' => 'Name can only contain English characters and numbers.']);
        } else if(mb_strlen($input['name']) <= 3) {
            return $this->echoError(['error' => 'Name must contain more than 3 characters.']);
        }

        if(!isset($input['email']) || $input['email'] == null) {
            return $this->echoError(['error' => 'Email field can not be empty.']);
        } else if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->echoError(['error' => 'Email address is not valid']);
        }

        if(!isset($input['password']) || $input['password'] == null) {
            return $this->echoError(['error' => 'Password can not be empty.']);
        } else if(mb_strlen($input['password']) <= 6) {
            return $this->echoError(['error' => 'Password must be 6 characters or more.']);
        }

        $existingUser = (array) $this->dm->selectCollection(USERS)->findOne(
            [
                'email' => $input['email']
//                '$or' => [
//                    ['email' => $input['email']],
//                    ['phoneNumber' => $input['phoneNumber']]
//                ]
            ]);

        if($existingUser)
        {
            return $this->echoError(['error' => 'A user with the same Email already exists.']);
        }

        $password = PasswordV1::passwordHash($input['password']);

        $user = [
            'name' => $input['name'] ?? null,
            'email' => $input['email'],
            'password' => $password,
            'access' => VeezeeUserAccessHelper::getTrialAccess(),
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime(),
        ];

        $admins = $this->dm->selectCollection(USERS)->find(['type' => 'admin'])->toArray();
        if(count($admins) <= 0) {
            $user['type'] = 'admin';
        }

        try
        {
            $insertResult = $this->dm->selectCollection(USERS)->insertOne($user);

            return $this->echoNormal([
                'name' => $user['name'],
                'email' => $user['email']
            ]);
        }
        catch (\Exception $e)
        {
            return $this->echoError(['error' => 'Unexpected error.']);
        }
    }

    public function info()
    {
        $authHeader = $this->getRequest()->headers->get('Authorization');
        $token = extractAuthorizationToken($authHeader);

        $userAndToken = $this->getUserAndToken($token);
        $user = $userAndToken['user'];

        if(isset($userAndToken['hasNewToken']) && $userAndToken === true) {
            $this->getResponse()->headers->add([
                'Authorization' => 'Bearer ' . $userAndToken['token']
            ]);
        }

        return $this->echoNormal([
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'access' => $user['access'] ?? VeezeeUserAccessHelper::getTrialAccess()
        ]);
    }

    public function login($input = null)
    {
        $input = $input ?? getJSONInput();
        $headers = $this->getRequest()->headers;
        $requestedWith = $headers->get('X-Requested-With');
        $requestedWithPieces = explode('/', $requestedWith);
        $client = $requestedWithPieces[count($requestedWithPieces) - 1];

        if($input['email'] == '' || $input['password'] == '') {
            return $this->echoError(['error' => 'Invalid login or user does not exist.']);
        }

        try
        {
            $user = (array) $this->dm->selectCollection(USERS)->findOne(
                [
                    'email' => $input['email']
                ]);

            if($user != null)
            {
                $hasGoogleSession = false;
                if(isset($user['sessions'])) {
                    foreach ($user['sessions'] as $session)
                    {
                        if(isset($session['google']))
                            $hasGoogleSession = true;
                    }
                }
                if((str_contains($user['email'], '@gmail.com') || $hasGoogleSession) && (!isset($user['password']) || $user['password'] == null)) {
                    // user have registered with Google and does not have a password yet
                    return $this->echoHttp(['error' => 'Account is created using Google. Please log in using the Google login button.'], 530);
                }

                if(PasswordV1::passwordVerify($input['password'], $user['password']) === true)
                {
                    $token = JWTHelper::encode([
                        '_id' => (string) $user['_id'],
                        'email' => $user['email'],
                        'name' => $user['name'],
                        'client' => $client
                    ]);

                    $this->dm->selectCollection(USERS)->updateOne(
                        ['_id' => new ObjectID($user['_id'])],
                        ['$push' => ['sessions' => [
                            'token' => $token,
                            'expiresIn' => JWTHelper::getNewTokenExpirationDate()
                        ]]]
                    );

                    // delete old sessions
                    $this->dm->selectCollection(USERS)->updateOne(
                        ['_id' => new ObjectID($user['_id'])],
                        ['$pull' => ['sessions' => [
                            'expiresIn' => ['$lt' => time()]
                        ]]]
                    );

                    $_SESSION['loggedIn'] = true;
                    $_SESSION['_id'] = (string)$user['_id'];
                    $_SESSION['type'] = $user['type'] ?? 'normal';
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['name'] = $user['name'];

                    // successful login
                    return $this->echoNormal([
                        'token' => $token,
                        'expiresIn' => JWTHelper::getNewTokenExpirationDate(),
                        'name' => $user['name'] ?? null,
                        'email' => $user['email'] ?? null,
                        'access' => $user['access'] ?? VeezeeUserAccessHelper::getTrialAccess()
                    ]);
                }
            }
        }
        catch (\Exception $e)
        {
            return $this->echoError(['error' => $e->getMessage()]);
        }

        return $this->echoHttp(['error' => 'Invalid login or user does not exist.'], 401);
    }

    public function updateNameAndPassword()
    {
        $input = getJSONInput();

        if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return $this->echoHttp(['error' => 'Authorization token does not exist.'], 401);
        }

        $authHeader = $this->getRequest()->headers->get('Authorization');
        $token = extractAuthorizationToken($authHeader);

        $accountController = new AccountController();
        $userAndToken = $accountController->getUserAndToken($token);

        if(isset($userAndToken['code']) && isset($userAndToken['error'])) {
            // error
            return $this->echoHttp(null, 401);
        }

        $user = $userAndToken['user'];

        if(isset($userAndToken['hasNewToken']) && $userAndToken === true) {
            $this->getResponse()->headers->add([
                'Authorization' => 'Bearer ' . $userAndToken['token']
            ]);
        }

        if(!isset($input['name']) || $input['name'] == null) {
            return $this->echoError(['error' => 'Name field can not be empty.']);
        } else if(!preg_match('/^[a-zA-Z0-9]+$/', $input['name'])) {
            return $this->echoError(['error' => 'Name can only contain English characters and numbers.']);
        } else if(mb_strlen($input['name']) <= 3) {
            return $this->echoError(['error' => 'Name must contain more than 3 characters.']);
        }

        $name = $input['name'];

        if(isset($input['password']) && $input['password'] != null) {
            if(mb_strlen($input['password']) <= 6) {
                return $this->echoError(['error' => 'Password must be 6 characters or more.']);
            }

            $password = PasswordV1::passwordHash($input['password']);

            $this->dm->selectCollection(USERS)->updateOne(
                ['_id' => new ObjectID($user['_id'])],
                ['$set' => ['password' => $password]]
            );
        }

        $this->dm->selectCollection(USERS)->updateOne(
            ['_id' => new ObjectID($user['_id'])],
            ['$set' => ['name' => $name]]
        );

        return $this->echoNormal([]);
    }

    public function validateLogin()
    {
        $input = getJSONInput();

        $userAndToken = $this->getUserAndToken($input['token']);

        if(isset($userAndToken['code']) && isset($userAndToken['error'])) {
            // error
            return $this->echoHttp(['error' => $userAndToken['error']], $userAndToken['code']);
        }

        $user = $userAndToken['user'];
        $token = $userAndToken['token'];
        $expiresIn = $userAndToken['expiresIn'];

        return $this->echoNormal([
            'token' => $token,
            'expiresIn' => $expiresIn,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'access' => $user['access'] ?? VeezeeUserAccessHelper::getTrialAccess()
        ]);
    }

    public function getUserAndToken($token, $newToken = false): array
    {
        $decodedPayload = JWTHelper::decode($token);

        if($decodedPayload === null) {
            return ['code' => 410, 'error' => 'You must log in again.'];
        }

        // check if login is still valid
        if($decodedPayload === -1) {
            $possibleNewToken = $this->attemptToRefreshUserLogin($token);

            if($possibleNewToken !== null) {
                // call the current function again this time with the new token
                return $this->getUserAndToken($possibleNewToken);
            }

            // nothing can be done here, user must log in again
            return ['code' => 440, 'error' => 'Your login is expired. Please log in again.'];
        }

        $user = (array) $this->dm->selectCollection(USERS)->findOne(
            [
                'sessions.token' => $token
            ]);

        if($user == null || ($user['_id'] != $decodedPayload['_id'])) {
            // error
            return ['code' => 410, 'error' => 'Unauthorized access.'];
        }

        return ['user' => $user, 'token' => $token, 'expiresIn' => $decodedPayload['exp'], 'hasNewToken' => $newToken];
    }

    public function attemptToRefreshUserLogin($expiredToken): ?string
    {
        try {
            $headers = $this->getRequest()->headers;
            $requestedWith = $headers->get('X-Requested-With');
            $requestedWithPieces = explode('/', $requestedWith);
            $client = $requestedWithPieces[count($requestedWithPieces) - 1];

            $payload = JWTHelper::decodeExpiredOrNonExpiringToken($expiredToken);

            $user = (array) $this->dm->selectCollection(USERS)->findOne(
                [
                    'sessions.token' => $expiredToken
                ]);

            if($user['_id'] != $payload['_id'] || $payload['client'] != $client) {
                // error
                return null;
            }

            if(isset($user['googleUserId']) && $user['googleUserId'] != null && (!isset($user['password']) || $user['password'] == null)) {
                // definitely a Google user
                $googleAccountController = new GoogleAccountController();
                $newAccessToken = $googleAccountController->getRefreshedAccessToken($user['session']['google']['accessToken'], $user['session']['google']['refreshToken']);

                $this->dm->selectCollection(USERS)->updateOne(
                    ['_id' => new ObjectID($user['_id'])],
                    ['$set' => ['session.google.accessToken' => $newAccessToken]]
                );
            }

            // update the token regardless
            $newToken = JWTHelper::encode([
                '_id' => (string) $user['_id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'client' => $client
            ]);

            $this->dm->selectCollection(USERS)->updateOne(
                ['_id' => new ObjectID($user['_id'])],
                ['$push' => ['sessions' => [
                    'token' => $newToken,
                    'expiresIn' => JWTHelper::getNewTokenExpirationDate()
                ]]]
            );

            return $newToken;
        } catch (\Exception $e) {
            return null;
        }
    }
}