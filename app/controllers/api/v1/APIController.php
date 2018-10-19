<?php

namespace App\Controllers\API\V1;

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Regex;
use Soda\Core\Http\Controller;
use App\Controllers\API\V1\Account\AccountController;

class APIController extends Controller
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
            return;
        }

        $user = $userAndToken['user'];

        $this->user = $user;
        if(isset($userAndToken['hasNewToken']) && $userAndToken === true) {
            $this->getResponse()->headers->add([
                'Authorization' => 'Bearer ' . $userAndToken['token']
            ]);
        }
    }

    protected function access($sub, $name)
    {
//        $subFolder = $this->getRequest()->query->get('sub');
//        $fileName = $this->getRequest()->query->get('name');

        $urlPieces = explode('/', $_SERVER['REQUEST_URI']);

        $fileName = $urlPieces[count($urlPieces) - 1];
        $subFolder = $urlPieces[count($urlPieces) - 2];

        $url = SITE_ADDRESS . "/resolve/tmp/?url=/content/music/albums/$subFolder/$fileName";
        header('Location: ' . $url);

//        $fp = fopen($url, 'rb');
//        foreach (get_headers($url) as $header)
//        {
//            header($header);
//        }
//        fpassthru($fp);
    }

    protected function homePageCollection()
    {
        $result = [];

        // build the home page collection

        $latestHeaders = $this->dm->selectCollection(HEADERS)->find(
            [
                'active' => true
            ],
            [
                'sort' => ['_id' => -1],
                'limit' => 13
            ]
        )->toArray();
        setAbsoluteUrlForTrackListWithEmbeddedAlbum($latestHeaders);
        if(count($latestHeaders) > 0) {
            $result[] = [
                'title' => null,
                'type' => 'Header',
                'headerList' => $latestHeaders
            ];
        }

        $genres = $this->dm->selectCollection(GENRES)->find([])->toArray();
        foreach ($genres as &$genre)
        {
            setAbsoluteUrlForRootImages($genre);

            unset($genre);
        }
        if(count($genres) > 0) {
            $result[] = [
                'title' => 'Genres',
                'type' => 'Genre',
                'genreList' => $genres
            ];
        }

        $latestTracks = $this->dm->selectCollection(TRACKS)->find(
            [
                '$or' => [
                    ['publishUpdate' => ['$exists' => false]],
                    ['publishUpdate' => true]
                ]
            ],
            [
                'sort' => ['_id' => -1],
                'limit' => 33
            ]
        )->toArray();
        setAbsoluteUrlForRootImages($latestTracks);
        setAbsoluteUrlForTrackListWithEmbeddedAlbum($latestTracks);
        $result[] = [
            'title' => 'Hot Tracks',
            'type' => 'Track',
            'trackList' => $latestTracks
        ];

        $latestAlbums = $this->dm->selectCollection(ALBUMS)->find(
            [
                'tracks.0' => ['$exists' => true],
                '$or' => [
                    ['publishUpdate' => ['$exists' => false]],
                    ['publishUpdate' => true]
                ]
            ], // find albums that have a tracks array with at least one track
            [
                'sort' => ['_id' => -1],
                'limit' => 32
            ]
        )->toArray();
        setAbsoluteUrlForAlbumsList($latestAlbums);
        if(count($latestAlbums) > 0) {
            $result[] = [
                'title' => 'New Releases',
                'type' => 'Album',
                'albumList' => $latestAlbums
            ];
        }

        $latestPlaylists = $this->dm->selectCollection(PLAYLISTS)->find(
            ['tracks.0' => ['$exists' => true]], // find playlists that have a tracks array with at least one track
            [
                'sort' => ['_id' => -1],
                'limit' => 32
            ]
        )->toArray();
        setAbsoluteUrlForPlaylistsList($latestPlaylists);
        if(count($latestPlaylists) > 0) {
            $result[] = [
                'title' => 'Playlists',
                'type' => 'Album',
                'albumList' => $latestPlaylists
            ];
        }

        if($this->user != null) {
            $userPlaylists = $this->user['playlists'];
            $activePlaylists = [];
            foreach ($userPlaylists as $playlist) {
                if(isset($playlist['tracks']) && count($playlist['tracks']) > 0) {
                    $activePlaylists[] = $playlist;
                }
            }
            $userPlaylists = $activePlaylists;
            setAbsoluteUrlForPlaylistsList($userPlaylists);
            if(count($userPlaylists) > 0) {
                $result[] = [
                    'title' => 'Your Playlists',
                    'type' => 'CompactAlbum',
                    'albumList' => $userPlaylists
                ];
            }
        }

        return $this->echoNormal($result);
    }

    protected function search()
    {
        $input = $this->getRequest()->query;

        $q = $input->get('q');
        $q = trim($q);

        if($q != '' && $q != null)
        {
            $result = [];

            $query = [];
            $query['$or'] = [
                ['title' => new Regex("$q", 'i')],
                ['album.title' => new Regex("$q", 'i')],
                ['album.artist.name' => new Regex("$q", 'i')],
            ];
            $tracksResult = $this->dm->selectCollection(TRACKS)->find($query, [
                'sort' => ['_id' => -1],
            ])->toArray();
            setAbsoluteUrlForRootImages($tracksResult);
            setAbsoluteUrlForTrackListWithEmbeddedAlbum($tracksResult);
            $result[] = [
                'title' => 'Tracks',
                'type' => 'Track',
                'trackList' => $tracksResult
            ];

            $query = [];
            $query['$or'] = [
                ['title' => new Regex("$q", 'i')],
                ['artist.name' => new Regex("$q", 'i')],
                ['tracks.title' => new Regex("$q", 'i')],
            ];
            $albumsResult = $this->dm->selectCollection(ALBUMS)->find($query, [
                'sort' => ['_id' => -1],
            ])->toArray();
            setAbsoluteUrlForAlbumsList($albumsResult);
            $result[] = [
                'title' => 'Albums',
                'type' => 'Album',
                'albumList' => $albumsResult
            ];

            $query = [];
            $query['$or'] = [
                ['title' => new Regex("$q", 'i')],
                ['tracks.title' => new Regex("$q", 'i')],
            ];
            $playlistsResult = $this->dm->selectCollection(PLAYLISTS)->find($query, [
                'sort' => ['_id' => -1],
            ])->toArray();
            setAbsoluteUrlForPlaylistsList($playlistsResult);
            $result[] = [
                'title' => 'Playlists',
                'type' => 'Album',
                'albumList' => $playlistsResult
            ];

            return $this->echoNormal($result);
        }
    }

    protected function latestSearchTrends()
    {
//        $this->dm->selectCollection(SEARCH_TRENDS)->insertMany([
//            ['title' => 'test 99'],
//            ['title' => 'test 77'],
//            ['title' => 'test 33'],
//            ['title' => 'test 11'],
//        ]);

        $trends = $this->dm->selectCollection(SEARCH_TRENDS)->aggregate(
            [
                [
                    '$group' => ['_id' => '$title', 'count' => ['$sum' => 1]]
                ],
                [
                    '$sort' => ['count' => -1]
                ],
                [
                    '$limit' => 10
                ]
            ]
        )->toArray();

        $result = [];
        foreach ($trends as $trend)
        {
            $result[] = $trend['_id'];
        }

        return $this->echoNormal($result);
    }

    protected function latestAlbumArts()
    {
        $albumArts = $this->dm->selectCollection(ALBUMS)->find(
            [
                'tracks.0' => ['$exists' => true],
                '$or' => [
                    ['publishUpdate' => ['$exists' => false]],
                    ['publishUpdate' => true]
                ]
            ],
            [
                'sort' => ['_id' => -1],
                'limit' => 20,
                'projection' => ['image' => 1]
            ]
        )->toArray();

        foreach ($albumArts as &$albumArt) {
            setAbsoluteUrlForRootImages($albumArt);
        }

        return $this->echoNormal($albumArts);
    }

    protected function albums()
    {
        $lastId = $this->getRequest()->query->get('lastId') ?? 0;

        $query = [
            'tracks.0' => ['$exists' => true],
            '$or' => [
                ['active' => ['$exists' => false]],
                ['active' => true]
            ]
        ];
        if($lastId != 0) {
            $query['_id'] = ['$lt' => new ObjectID($lastId)];
        }
        $albums = $this->dm->selectCollection(ALBUMS)->find(
            $query,
            [
                'sort' => ['_id' => -1],
                'limit' => 32,
            ]
        )->toArray();
        setAbsoluteUrlForAlbumsList($albums);

        return $this->echoNormal($albums);
    }

    protected function playlists()
    {
        $lastId = $this->getRequest()->query->get('lastId') ?? 0;

        $query = [
            'tracks.0' => ['$exists' => true],
            '$or' => [
                ['active' => ['$exists' => false]],
                ['active' => true]
            ]
        ];
        if($lastId != 0) {
            $query['_id'] = ['$lt' => new ObjectID($lastId)];
        }
        $playlists = $this->dm->selectCollection(PLAYLISTS)->find(
            $query,
            [
                'sort' => ['_id' => -1],
                'limit' => 32,
            ]
        )->toArray();
        setAbsoluteUrlForPlaylistsList($playlists);

        return $this->echoNormal($playlists);
    }

    protected function tracks()
    {
        $lastId = $this->getRequest()->query->get('lastId') ?? 0;

        $query = [
            '$or' => [
                ['active' => ['$exists' => false]],
                ['active' => true]
            ]
        ];
        if($lastId != 0) {
            $query['_id'] = ['$lt' => new ObjectID($lastId)];
        }
        $tracks = $this->dm->selectCollection(TRACKS)->find(
            $query,
            [
                'sort' => ['_id' => -1],
                'limit' => 32,
            ]
        )->toArray();

        setAbsoluteUrlForRootImages($tracks);
        setAbsoluteUrlForTrackListWithEmbeddedAlbum($tracks);

        return $this->echoNormal($tracks);
    }

    protected function genres()
    {
        $genres = $this->dm->selectCollection(GENRES)->find([])->toArray();

        foreach ($genres as &$genre)
        {
            setAbsoluteUrlForRootImages($genre);

            unset($genre);
        }

        return $this->echoNormal($genres);
    }

    protected function album($_id)
    {
        $album = (array) $this->dm->selectCollection(ALBUMS)->findOne(['_id' => new ObjectID($_id)]);
        // create an array with only one album because the helper function takes an array not an object
        $a = [$album];

        setAbsoluteUrlForAlbumsList($a);

        return $this->echoNormal($a[0]);
    }
}