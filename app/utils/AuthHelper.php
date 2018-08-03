<?php

namespace App\Utils;

use GuzzleHttp\Client;
use MongoDB\BSON\ObjectId;
use Soda\Core\Http\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthHelper extends Controller
{
    private $dm;
    private $client;

    public function __construct()
    {
        parent::__construct();

        $options = [
            'base_uri' => SITE_ADDRESS,
            'timeout'  => 2.0,
        ];

        if(USE_CUSTOM_OPEN_SSL_CERT)
        {
            $options['verify'] = VERIFY_CUSTOM_OPEN_SSL_CERT ? CUSTOM_OPEN_SSL_CERT_PATH : false;
        }

        $this->client = new Client($options);
        $this->dm = $this->getMongoDB()->getDM();
    }

    // do not use
    public function validateAuth($jwt): array
    {
        try
        {
            $response = $this->client->request('POST', '/api/v1/account/validate-login', ['json' => ['token' => $jwt, 'type' => 'universal']]);
        }
        catch (\Exception $e)
        {
            return ['success' => false];
        }

        if($response->getStatusCode() !== 200)
        {
            // error
            return ['success' => false];
        }

        $body = $response->getBody();
        $payload = JWTHelper::decode($body);

        return ['success' => true, 'payload' => $payload];
    }

    public function validateToken($token, $type): ?array
    {
        try
        {
            $payload = JWTHelper::decode($token);

            if($payload == null || !isset($payload['_id']))
            {
                // error
                return null;
            }

            $user = (array) $this->dm->selectCollection(USERS)->findOne(
                [
                    '_id' => new ObjectID(((array)$payload['_id'])['$oid'])
                ]);

            if(!isset($user['tokens'][$type]) || $user['tokens'][$token] !== $token)
            {
                return null;
            }

            return [
                '_id' => $user['_id'],
                'firstName' => validateRawValue(@$user['firstName']),
                'lastName' => validateRawValue(@$user['lastName']),
                'name' => validateRawValue(@$user['firstName']) . ' ' . validateRawValue(@$user['lastName']),
                'avatar' => validateRawValue(@$user['avatar']),
                'email' => validateRawValue(@$user['email']),
                'phoneNumber' => validateRawValue(@$user['phoneNumber'])
            ];
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    public static function getLocallyStoredToken()
    {
        if(isset($_COOKIE['token']) && $_COOKIE['token'] != '')
        {
            return $_COOKIE['token'];
        }

        return null;
    }

    public static function setLocallyStoredToken($token)
    {
        setcookie('token', $token, time() + (86400 * 20), '/','.' . MAIN_DOMAIN); // 86400 = 1 day, all together 20 days
    }

    public static function getSession($index = null)
    {
        if(is_null($index))
        {
            return $_SESSION;
        }
        else
        {
            return isset($_SESSION[$index]) ? $_SESSION[$index] : null;
        }
    }

    public static function setSession(array $data)
    {
        foreach ($data as $key => $value)
        {
            $_SESSION[$key] = $value;
        }
    }

    public static function setLoggedIn()
    {
        $_SESSION['loggedIn'] = true;
    }

    public static function isLoggedIn()
    {
        if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true && isset($_SESSION['_id']))
        {
            return true;
        }

        return false;
    }

    public static function getUserType()
    {
        if(!AuthHelper::isLoggedIn())
            return null;

        return $_SESSION['type'];
    }

    public static function logOut()
    {
        foreach (array_keys($_SESSION) as $key)
        {
            unset($_SESSION[$key]);
        }

        if (isset($_COOKIE['token']))
        {
            unset($_COOKIE['token']);
            setcookie('token', '', time() - 3600, '/', '.' . MAIN_DOMAIN); // empty value and old timestamp
        }
    }
}