<?php

//$headersService = new \HeadersService();
//$headersService->addTrackToHeader('5b49ca96b3cdceedf87f1575', '5b49caaeb3cdce2ea93e4336');
//$headersService->refreshHeader('5b49ca96b3cdceedf87f1575', '5b496529b3cdceedf87f1572');

//
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Cache-Control');
header('Access-Control-Allow-Methods: GET, POST, PUT');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // The request is using the POST method
    die();
}

use Phroute\Phroute\RouteCollector;

$router = new RouteCollector();

//if(!\App\Utils\AuthHelper::isLoggedIn())
//{
//    $token = \App\Utils\AuthHelper::getLocallyStoredToken();
//    if($token !== null)
//    {
//        // now verify the token and set the session accordingly
//        $authHelper = new \App\Utils\AuthHelper();
//
//        $payload = $authHelper->validateToken($token, 'universal');
//
//        if($payload !== null && is_array($payload))
//        {
//            // we have the payload
//            $payload['token'] = $token;
//
//            // log the user IN
//            \App\Utils\AuthHelper::setSession($payload);
//            \App\Utils\AuthHelper::setLoggedIn();
//        }
//        else
//        {
//            \App\Utils\AuthHelper::logOut();
//        }
//    }
//}

$router->filter('loggedIn', function () {
    if (!loggedIn()) {
        redirect('/account/login?redirect=' . $_SERVER['REQUEST_URI']);
    }
});

$router->filter('loggedInAndAdmin', function () {
    if(!\App\Utils\AuthHelper::isLoggedIn()) {
        redirect('/account/login?redirect=' . $_SERVER['REQUEST_URI']);
    }

    if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
        notFound();
    }
});

/**
 * Define your routes after this comment!
 */

$router->group(['prefix' => 'account'], function($router) {
    $controller = '\App\Controllers\AccountController';
    $router->get(['/login', 'login'], [$controller, 'login']);
    $router->get(['/reset-password', 'reset-password'], [$controller, 'resetPassword']);
    $router->get(['/register', 'register'], [$controller, 'register']);
    $router->get(['/logout', 'logout'], [$controller, 'logOut']);

    $router->group(['before' => 'loggedIn'], function($router) {
        $controller = '\App\Controllers\AccountController';
        $router->get(['/info', 'info'], [$controller, 'info']);
        $router->get(['/edit', 'edit'], [$controller, 'edit']);
    });
});


$router->group(['prefix' => 'api'], function ($router) {
    $router->group(['prefix' => 'v1'], function ($router) {

        $router->group(['prefix' => 'account'], function($router) {

            $controller = '\App\Controllers\API\V1\Account\AccountController';
            $router->get('/info', [$controller, 'info']);
            $router->post('/login', [$controller, 'login']);
            $router->post('/validate-login', [$controller, 'validateLogin']);
            $router->post('/update-name-and-password', [$controller, 'updateNameAndPassword']);
            if(USERS_API_ENABLED) {
                $router->post('/register', [$controller, 'register']);
                $router->group(['prefix' => 'google'], function($router) {
                    $controller = '\App\Controllers\API\V1\Account\GoogleAccountController';
                    $router->post('/process-login', [$controller, 'processLogin']);
                });
                $router->group(['prefix' => 'vex'], function($router) {
                    $controller = '\App\Controllers\API\V1\Account\VEXController';
                    $router->post('/played-track', [$controller, 'playedTrack']);
                    $router->get('/user-tracks-history', [$controller, 'getUserTracksHistory']);
                    $router->delete('/delete-track-from-history', [$controller, 'deleteTrackFromHistory']);
                });
            }
            $router->group(['prefix' => 'playlists'], function($router) {
                $controller = '\App\Controllers\API\V1\Account\PlaylistsController';
                $router->get('/get', [$controller, 'get']);
                $router->post('/new', [$controller, 'new']);
                $router->delete('/delete/{_id}', [$controller, 'delete']);
                $router->post('/tracks/add', [$controller, 'addTo']);
            });
        });

        $router->group(['prefix' => 'get'], function($router) {
            $controller = '\App\Controllers\API\V1\APIController';
            $router->get('/latest-album-arts', [$controller, 'latestAlbumArts']);
            $router->get('/latest-search-trends', [$controller, 'latestSearchTrends']);
            $router->get('/home-page-collection', [$controller, 'homePageCollection']);
            $router->get('/search', [$controller, 'search']);
            $router->get('/albums', [$controller, 'albums']);
            $router->get('/playlists', [$controller, 'playlists']);
            $router->get('/tracks', [$controller, 'tracks']);
            $router->get('/genres', [$controller, 'genres']);
            $router->get('/album/{_id}', [$controller, 'album']);
        });

        $controller = '\App\Controllers\API\V1\APIController';
        $router->get('/access/{sub}/{name}', [$controller, 'access']);
    });
});

$router->group(['prefix' => ADMIN_URL, 'before' => 'loggedInAndAdmin'], function ($router) {
    $router->get('/', ['\App\Controllers\Admin\AdminController', 'index']);

    $router->group(['prefix' => 'artists'], function ($router) {
        $controller = '\App\Controllers\Admin\ArtistsController';
        $router->get('/', [$controller, 'index']);
        $router->get('/new', [$controller, 'new']);
        $router->post('/new-post', [$controller, 'newPost']);
        $router->get('/edit/{_id}', [$controller, 'edit']);
        $router->post('/edit-post/{_id}', [$controller, 'editPost']);
    });

    $router->group(['prefix' => 'playlists'], function ($router) {
        $controller = '\App\Controllers\Admin\PlaylistsController';
        $router->get('/', [$controller, 'index']);
        $router->get('/get', [$controller, 'get']);
        $router->get('/get-tracks/{_id}', [$controller, 'getTracks']);
        $router->get('/new', [$controller, 'new']);
        $router->post('/new-post', [$controller, 'newPost']);
        $router->get('/edit/{_id}', [$controller, 'edit']);
        $router->post('/edit-post/{_id}', [$controller, 'editPost']);
        $router->get('/choose-tracks/{_id}', [$controller, 'chooseTracks']);
        $router->post('/choose-tracks-post/{_id}', [$controller, 'chooseTracksPost']);
        $router->post('/update-all-playlists', [$controller, 'updateAllPlaylists']);
        $router->post('/remove-image-post/{_id}', [$controller, 'removeImagePost']);
        $router->get('/delete/{_id}', [$controller, 'delete']);
        $router->post('/delete-post/{_id}', [$controller, 'deletePost']);
    });

    $router->group(['prefix' => 'albums'], function ($router) {
        $controller = '\App\Controllers\Admin\AlbumsController';
        $router->get('/', [$controller, 'index']);
        $router->get('/new', [$controller, 'new']);
        $router->post('/new-post', [$controller, 'newPost']);
        $router->get('/edit/{_id}', [$controller, 'edit']);
        $router->post('/edit-post/{_id}', [$controller, 'editPost']);
        $router->get('/single-track/{_albumId}/{_trackId}', [$controller, 'singleTrack']);
        $router->post('/single-track-post/{_albumId}/{_trackId}', [$controller, 'singleTrackPost']);
        $router->get('/get-albums', [$controller, 'getAlbums']);
        $router->get('/get-tracks/{_id}', [$controller, 'getTracks']);
        $router->get('/upload-tracks/{_id}', [$controller, 'uploadTracks']);
        $router->post('/upload-track-post/{_id}', [$controller, 'uploadTrackPost']);
        $router->post('/add-tracks-from-other-albums-post/{_id}', [$controller, 'addTracksFromOtherAlbumsPost']);
        $router->post('/update-already-uploaded-tracks-post/{_id}', [$controller, 'updateAlreadyUploadedTracks']);
        $router->post('/remove-image-post/{_id}', [$controller, 'removeImagePost']);
        $router->get('/delete/{_id}', [$controller, 'delete']);
        $router->post('/delete-post/{_id}', [$controller, 'deletePost']);
    });

    $router->group(['prefix' => 'genres'], function ($router) {
        $controller = '\App\Controllers\Admin\GenresController';
        $router->get('/', [$controller, 'index']);
        $router->get('/new', [$controller, 'new']);
        $router->post('/new-post', [$controller, 'newPost']);
        $router->get('/edit/{_id}', [$controller, 'edit']);
        $router->post('/edit-post/{_id}', [$controller, 'editPost']);
    });

    $router->group(['prefix' => 'users'], function ($router) {
        $controller = '\App\Controllers\Admin\UsersController';
        $router->get('/', [$controller, 'index']);
    });
});

$router->get('/', ['\App\Controllers\HomeController', 'index']);
$router->get('/insert', ['\App\Controllers\HomeController', 'insertData']);