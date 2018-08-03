<?php

namespace App\Controllers\API\V1;

use MongoDB\BSON\ObjectID;
use MongoDB\Driver\Exception\InvalidArgumentException;
use Soda\Core\Http\Controller;

class AlbumsController extends Controller
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

    protected function latest()
    {
        $params = $this->getRequest()->query;

        // build the home page playlist collection
        $playlists = $this->dm->selectCollection(PLAYLISTS)->find([], ['limit' => 4]);
    }
}