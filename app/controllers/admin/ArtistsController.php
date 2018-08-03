<?php

namespace App\Controllers\Admin;

use App\Services\AssetsService;
use MongoDB;
use MongoDB\BSON\ObjectID;
use Soda\Core\Http\Controller;

class ArtistsController extends Controller
{
    private $_admin_url;
    private $dm;

    public function __construct()
    {
        parent::__construct();
        $this->_admin_url = ADMIN_URL;
        $this->dm = $this->getMongoDB()->getDM();
    }

    public function beforeActionExecution($action_name, $action_arguments)
    {
        parent::beforeActionExecution($action_name, $action_arguments);
    }

    protected function index()
    {
        $list = $this->dm->selectCollection(ARTISTS)->find([])->toArray();

        return $this->render('admin.artists.index', ['opr' => getOpR(), 'list' => $list]);
    }

    protected function new()
    {
        return $this->render('admin.artists.new', ['opr' => getOpR()]);
    }

    protected function newPost()
    {
        $form = $this->getRequest()->request;

        $name = $form->get('name');

        if($name == null)
        {
            setOpR(false, 'Name can not be null');
            return redirect("/$this->_admin_url/artists/new");
        }

        $entries = ['name' => $name];

        $this->dm->selectCollection(ARTISTS)->insertOne($entries);

        return redirect("/$this->_admin_url/artists");
    }

    protected function edit($_id)
    {
        $artist = $this->dm->selectCollection(ARTISTS)->findOne(['_id' => new ObjectID($_id)]);

        return $this->render('admin.artists.edit', ['opr' => getOpR(), 'artist' => $artist]);
    }

    protected function editPost($_id)
    {
        $form = $this->getRequest()->request;

        $name = $form->get('name');

        if($name == null)
        {
            setOpR(false, 'Name can not be null');
            return redirect("/$this->_admin_url/artists/edit/" . $_id);
        }

        $entries = ['name' => $name];

        if(isset($_FILES['image']) && file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $entries['image'] = AssetsService::saveUploadedArtwork('image');
        }

        $newArtist = $this->dm->selectCollection(ARTISTS)->findOneAndUpdate(
            ['_id' => new ObjectID($_id)],
            ['$set' => $entries],
            ['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );

        // update embedded artists in albums
        $this->dm->selectCollection(ALBUMS)->updateMany(
            ['artist._id' => new ObjectID($_id)],
            ['$set' => ['artist' => $newArtist]]
        );

        // update embedded artists in
        $this->dm->selectCollection(PLAYLISTS)->updateMany(
            ['tracks.album.artist._id' => new ObjectId($_id)],
            ['$set' => ['tracks.$.album.artist' => $newArtist]]
        );

        // update headers (for albums)
        $this->dm->selectCollection(HEADERS)->updateMany(
            ['album.artist._id' => new ObjectId($_id)],
            ['$set' => ['artist' => $newArtist, 'album.artist' => $newArtist]]
        );

        // update headers (for playlists)
        $this->dm->selectCollection(PLAYLISTS)->updateMany(
            ['album.tracks.album.artist._id' => new ObjectId($_id)],
            ['$set' => ['album.tracks.$.album.artist' => $newArtist]]
        );

        return redirect("/$this->_admin_url/artists");
    }
}