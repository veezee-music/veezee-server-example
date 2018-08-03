<?php

namespace App\Controllers\API\V1\Account;

use App\Utils\AuthHelper;
use App\Utils\JWTHelper;
use App\Utils\PasswordV1;
use App\Utils\VeezeeUserAccessHelper;
use MongoDB;
use MongoDB\BSON\ObjectID;
use Soda\Core\Http\Controller;
use MongoDB\BSON\Regex;

class PlaylistsController extends Controller
{
    private $dm;
    private $user;

    public function __construct()
    {
        parent::__construct();
        $this->dm = $this->getMongoDB()->getDM();
    }

    public function beforeActionExecution($action_name, $action_arguments)
    {
        parent::beforeActionExecution($action_name, $action_arguments);

        $authHeader = $this->getRequest()->headers->get('Authorization');
        $token = extractAuthorizationToken($authHeader);

        $accountController = new AccountController();
        $userAndToken = $accountController->getUserAndToken($token);

        if(isset($userAndToken['code']) && isset($userAndToken['error'])) {
            // error
            return $this->echoHttp(null, 401);
        }

        $user = $userAndToken['user'];

        $this->user = $user;
        if(isset($userAndToken['hasNewToken']) && $userAndToken === true) {
            $this->getResponse()->headers->add([
                'Authorization' => 'Bearer ' . $userAndToken['token']
            ]);
        }
    }

    protected function new()
    {
        $input = getJSONInput();

        $title = $input['title'] ?? null;

        if($title == null) {
            return $this->echoError(['error' => 'Playlist title can not be empty;']);
        }

        $playlist = [
            '_id' => new ObjectID(),
            'title' => $title,
            'tracks' => [],
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime(),
        ];

        $this->dm->selectCollection(USERS)->updateOne(
            ['_id' => new ObjectID($this->user['_id'])],
            ['$push' => ['playlists' => $playlist]]
        );

        return $this->echoHttp($playlist, 201);
    }

    protected function delete($_id)
    {
        if($_id == null) {
            return $this->echoError(['error' => 'Playlist id must be provided']);
        }

        $playlistId = new ObjectID($_id);

        if(isset($this->user['playlists'])) {
            foreach($this->user['playlists'] as $userPlaylist)
            {
                if((string)$userPlaylist['_id'] == (string)$playlistId) {
                    $this->dm->selectCollection(USERS)->updateOne(
                        ['_id' => new ObjectID($this->user['_id'])],
                        ['$pull' => ['playlists' => ['_id' => new ObjectID($userPlaylist['_id'])]]]
                    );

                    return $this->echoNormal([]);
                }
            }
        }

        return $this->echoHttp([], 202);
    }

    protected function get()
    {
        $result = (array) $this->dm->selectCollection(USERS)->findOne(
            ['_id' => new ObjectID($this->user['_id'])],
            [
                'projection' => ['playlists' => 1]
            ]
        );

        if(!isset($result['playlists'])) {
            return $this->echoNormal([]);
        }

        return $this->echoNormal(array_reverse($result['playlists']));
    }

    protected function addTo()
    {
        try {
            $input = getJSONInput();

            $playlist = $input['playlist'] ?? null;
            $playlist['_id'] = $playlist['_id']['$oid'];

            $track = $input['track'] ?? null;
            $track['_id'] = $track['_id']['$oid'];

            if($playlist == null) {
                return $this->echoError(['error' => 'Playlist can not be empty']);
            }
            if($track == null) {
                return $this->echoError(['error' => 'Track can not be empty']);
            }

            $existingTrack = (array) $this->dm->selectCollection(TRACKS)->findOne(['_id' => new ObjectID($track['_id'])]);

            foreach($this->user['playlists'] as $userPlaylist)
            {
                if((string)$userPlaylist['_id'] == (string)$playlist['_id']) {
                    foreach($userPlaylist['tracks'] as $playlistTrack)
                    {
                        if((string)$playlistTrack['_id'] == (string)$track['_id']) {
                            return $this->echoNormal([]);
                        }
                    }
                }
            }

            $this->dm->selectCollection(USERS)->updateOne(
                ['_id' => new ObjectID($this->user['_id']), 'playlists._id' => new ObjectID($playlist['_id'])],
                ['$push' => ['playlists.$.tracks' => $existingTrack]]
            );
        } catch (\Exception $e) {
            return $this->echoError(['error' => $e->getMessage()]);
        }


        return $this->echoHttp([], 201);
    }
}