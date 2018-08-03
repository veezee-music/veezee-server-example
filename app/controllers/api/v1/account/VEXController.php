<?php

namespace App\Controllers\API\V1\Account;

use MongoDB\BSON\ObjectId;
use Soda\Core\Http\Controller;

// veezee experience -> VEX
class VEXController extends Controller
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

    protected function playedTrack()
    {
        $input = getJSONInput();

        if(!isset($input['trackId'])) {
            return $this->echoError(['error' => 'Track id is not set.']);
        }

        $track = (array) $this->dm->selectCollection(TRACKS)->findOne(['_id' => new ObjectId($input['trackId'])]);

        if(count($track) <= 0) {
            return $this->echoError([]);
        }

        $this->dm->selectCollection(USERS)->updateOne(
            ['_id' => new ObjectId($this->user['_id'])],
            ['$push' => ['vex.history.tracks' => $track]]
        );

        return $this->echoNormal([]);
    }

    protected function getUserTracksHistory()
    {
        $tracks = array_reverse($this->user['vex']['history']['tracks']);
        $tracks = array_unique_assoc($tracks);

        setAbsoluteUrlForRootImages($tracks);
        setAbsoluteUrlForTrackListWithEmbeddedAlbum($tracks);

        return $this->echoNormal($tracks);
    }

    protected function deleteTrackFromHistory()
    {
        $trackId = $this->getRequest()->query->get('trackId') ?? null;

        if($trackId == null) {
            return $this->echoError(['error' => 'Track id is not set.']);
        }

        $this->dm->selectCollection(USERS)->updateOne(
            ['_id' => new ObjectId($this->user['_id'])],
            ['$pull' => ['vex.history.tracks' => ['_id' => new ObjectId($trackId)]]]
        );

        return $this->echoNormal([]);
    }
}