<?php

namespace App\Controllers\API\V1\Account;

use App\Utils\JWTHelper;
use App\Utils\VeezeeUserAccessHelper;
use Google_Client;
use MongoDB;
use MongoDB\BSON\ObjectId;
use Soda\Core\Http\Controller;

class GoogleAccountController extends Controller
{
    private $dm;
    private $googleClient;

    public function __construct()
    {
        parent::__construct();
        $this->dm = $this->getMongoDB()->getDM();
        $this->googleClient = new Google_Client();
        try
        {
            $this->googleClient->setAuthConfig(__DIR__ . '/../../../../../config/client_secret.json');
        } catch (\Google_Exception $e)
        {
            return $this->echoError(['error' => 'Google Client not configured properly.']);
        }
        $this->googleClient->setAccessType('offline');
    }

    public function beforeActionExecution($action_name, $action_arguments)
    {
        parent::beforeActionExecution($action_name, $action_arguments);
    }

    public function verifyGoogleTokenAndGetUser($input): array
    {
        try
        {
            $headers = $this->getRequest()->headers;
            $requestedWith = $headers->get('X-Requested-With');
            $requestedWithPieces = explode('/', $requestedWith);
            $client = $requestedWithPieces[count($requestedWithPieces) - 1];

            // verify the token using Google APIs SDK
            $payload = $this->googleClient->verifyIdToken($input['idToken'] ?? $input['id_token']);
            if ($payload) {
                $user = $this->getUserAndUpdateInfoFromPayload($payload);
                if($user == null) {
                    $user = $this->createUser($payload);
                }

                if($user == null) {
                    throw new \Exception('User could not be created.');
                }

                $token = JWTHelper::encode([
                    '_id' => (string) $user['_id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'client' => $client
                ]);

                $entries = [];
                $entries['token'] = $token;
                $entries['google'] = [
                    'idToken' => $input['id_token'],
                    'accessToken' => $input['access_token'],
                    'refreshToken' => $input['refresh_token']
                ];

                $this->dm->selectCollection(USERS)->updateOne(
                    ['_id' => new ObjectID($user['_id'])],
                    ['$push' => ['sessions' => $entries]]
                );

                // delete old sessions
                $this->dm->selectCollection(USERS)->updateOne(
                    ['_id' => new ObjectID($user['_id'])],
                    ['$pull' => ['sessions' => [
                        'expiresIn' => ['$lt' => time()]
                    ]]]
                );

                $ret = [
                    'code' => 200,
                    'token' => $token,
                    'expiresIn' => JWTHelper::getNewTokenExpirationDate(),
                    'name' => $user['name'] ?? null,
                    'email' => $user['email'] ?? null,
                    'access' => $user['access'] ?? VeezeeUserAccessHelper::getTrialAccess()
                ];
                return $ret;
            } else {
                $ret = ['code' => 401, 'error' => 'Invalid login. Please try again.'];
                return $ret;
            }
        }
        catch (\Exception $e) {
            $ret = ['code' => 500, 'error' => $e->getMessage()];
            return $ret;
        }
    }

    public function processLogin()
    {
        $input = getJSONInput();

        try
        {
            $credentials = $this->googleClient->fetchAccessTokenWithAuthCode($input['serverAuthCode']);
            $res = $this->verifyGoogleTokenAndGetUser($credentials);

            if($res == null || !isset($res['token'])) {
                throw new \Exception('Either request or Google response are not correct.');
            }

            return $this->echoHttp([
                'token' => $res['token'],
                'expiresIn' => $res['expiresIn'],
                'name' => $res['name'],
                'email' => $res['email'],
                'access' => $res['access']
            ], $res['code']);
        }
        catch (\Exception $e)
        {
            return $this->echoError(['error' => $e->getMessage()]);
        }
    }

    public function createUser($payload): ?array
    {
        $user = [
            'googleUserId' => $payload['sub'] ?? null,
            'email' => strtolower($payload['email']),
            'emailVerified' => isset($payload['email_verified']) && $payload['email_verified'] == 1 ? true : false,
            'name' => $payload['name'] ?? null,
            'firstName' => $payload['given_name'] ?? null,
            'lastName' => $payload['family_name'] ?? null,
            'originalImage' => $payload['picture'] ?? null,
            'access' => VeezeeUserAccessHelper::getTrialAccess(),
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime(),
        ];

        try
        {
            $insertResult = $this->dm->selectCollection(USERS)->insertOne($user);
            $user['_id'] = $insertResult->getInsertedId();

            return $user;
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    public function getUserAndUpdateInfoFromPayload($payload): ?array
    {
        $existingUser = (array) $this->dm->selectCollection(USERS)->findOne(
            ['email' => strtolower($payload['email'])]
        );

        if($existingUser != null)
        {
            $updatedEntries = [];
            if($payload['name'] != null) {
                $updatedEntries['name'] = $payload['name'];
            }
            if($payload['given_name'] != null) {
                $updatedEntries['firstName'] = $payload['given_name'];
            }
            if($payload['family_name'] != null) {
                $updatedEntries['lastName'] = $payload['family_name'];
            }
            if($payload['picture'] != null) {
                $updatedEntries['originalImage'] = $payload['picture'];
            }
            if(count($updatedEntries) > 0) {
                $updatedEntries['updatedAt'] = new \DateTime();
            }
            $updatedUser = $this->dm->selectCollection(USERS)->findOneAndUpdate(
                ['email' => $payload['email']],
                ['$set' => $updatedEntries],
                ['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
            );

            return $updatedUser;
        } else {
            return null;
        }
    }

    public function getRefreshedAccessToken($accessToken, $refreshToken) {
        try {
            $this->googleClient->setAccessToken($accessToken);

            if($this->googleClient->isAccessTokenExpired()) {
                $newAccessToken = $this->googleClient->fetchAccessTokenWithRefreshToken($refreshToken);

                return $newAccessToken;
            }

            return $accessToken;
        } catch (\Exception $e) {
            return null;
        }
    }
}