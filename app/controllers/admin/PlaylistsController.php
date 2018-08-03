<?php

namespace App\Controllers\Admin;

use App\Controllers\SharedController;
use MongoDB;
use MongoDB\BSON\ObjectID;
use Soda\Core\Http\Controller;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Extension;
use Upload\Validation\Size;

class PlaylistsController extends Controller
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
        $list = $this->dm->selectCollection(PLAYLISTS)->find([])->toArray();

        return $this->render('admin.playlists.index', ['opr' => getOpR(), 'list' => $list]);
    }

    protected function new()
    {
        return $this->render('admin.playlists.new', ['opr' => getOpR()]);
    }

    protected function newPost()
    {
        $form = $this->getRequest()->request;

        $title = $form->get('title');

        if($title == null)
        {
            setOpR(false, 'Title can not be null');
            return redirect("/$this->_admin_url/playlists/new");
        }

        $entries = [
            'title' => $title,
            'tracks' => [],
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime(),
        ];

        $this->dm->selectCollection(PLAYLISTS)->insertOne($entries);

        return redirect("/$this->_admin_url/playlists");
    }

    protected function edit($_id)
    {
        $playlist = $this->dm->selectCollection(PLAYLISTS)->findOne(['_id' => new ObjectID($_id)]);

        return $this->render('admin.playlists.edit', ['opr' => getOpR(), 'playlist' => $playlist]);
    }

    protected function editPost($_id)
    {
        $form = $this->getRequest()->request;

        $title = $form->get('title');
        $headerActive = $form->get('headerActive');

        $primaryColor = $form->get('primaryColor');
        $accentColor = $form->get('accentColor');

        if($title == null)
        {
            setOpR(false, 'Title can not be null');
            return redirect('/admin/playlists/edit/' . $_id);
        }

        if(isset($_FILES['image']) && file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $imageFileName = \AssetsService::saveUploadedArtwork('image');
        }

        if(isset($_FILES['headerImage']) && file_exists($_FILES['headerImage']['tmp_name']) && is_uploaded_file($_FILES['headerImage']['tmp_name']))
        {
            $headerImageFileName = \AssetsService::saveUploadedHeaderImage('headerImage');
        }

        $existingPlaylist = (array) $this->dm->selectCollection(PLAYLISTS)->findOne(
            ['_id' => new ObjectID($_id)]
        );

        $entries = [
            'title' => $title,
            'updatedAt' => new \DateTime(),
        ];

        if(isset($imageFileName)) {
            $entries['image'] = $imageFileName;
        }

        if(isset($entries['image']) || (isset($existingPlaylist['image']) && $existingPlaylist['image'] != null)) {
            $entries['colors'] = [
                'primaryColor' => $primaryColor,
                'accentColor' => $accentColor
            ];
        }

        $insertContainerInHeader = false;
        if(isset($headerImageFileName)) {
            $header = [
                'active' => $headerActive != null ? true : false,
                'image' => $headerImageFileName,
                'title' => $title,
                'type' => 'playlist',
                'updatedAt' => new \DateTime()
            ];
            $header['playlist'] = ['_id' => new ObjectID($_id)];

            $existingHeader = (array) $this->dm->selectCollection(HEADERS)->findOne(
                ['playlist._id' => new ObjectID($_id)]
            );
            if($existingHeader != null) {
                // update the existing header
                $existingHeader = array_replace($existingHeader, $header);

                $this->dm->selectCollection(HEADERS)->replaceOne(
                    ['_id' => new ObjectID($existingHeader['_id'])],
                    $existingHeader
                );
                $header['_id'] = new ObjectID($existingHeader['_id']);
            } else {
                // create a new one
                $res = $this->dm->selectCollection(HEADERS)->insertOne($header);

                $header['_id'] = $res->getInsertedId();
            }

            $entries['header'] = $header;
            $insertContainerInHeader = true;
        } else if(isset($existingPlaylist['header'])) {
            $entries['header'] = $existingPlaylist['header'];
            $entries['header']['active'] = $headerActive != null ? true : false;

            $this->dm->selectCollection(HEADERS)->updateOne(
                ['playlist._id' => new ObjectID($_id)],
                ['$set' => ['active' => $entries['header']['active']]]
            );
            $insertContainerInHeader = true;
        }

        $newPlaylist = $this->dm->selectCollection(PLAYLISTS)->findOneAndUpdate(
            ['_id' => new ObjectID($_id)],
            ['$set' => $entries],
            ['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );

        if($insertContainerInHeader) {
            $this->dm->selectCollection(HEADERS)->updateOne(
                ['_id' => new ObjectID($newPlaylist['header']['_id'])],
                ['$set' => ['album' => $newPlaylist]]
            );
        }

        setOpR(true, 'Success.');
        return redirect("/$this->_admin_url/playlists/edit/" . $_id);
    }

    protected function removeImagePost($_id)
    {
        try {
            \PlaylistsService::removePlaylistImage($_id);

            setOpR(true, 'Success.');
        } catch (\Exception $e) {
            setOpR(false, $e->getMessage());
        }

        return $this->echoNormal([]);
    }

    protected function chooseTracks($_id)
    {
        $playlist = $this->dm->selectCollection(PLAYLISTS)->findOne(['_id' => new ObjectID($_id)]);

        return $this->render('admin.playlists.choose-tracks', ['opr' => getOpR(), 'playlist' => $playlist]);
    }

    protected function chooseTracksPost($_id)
    {
        $input = getJSONInput();

        foreach($input['tracks'] as &$track)
        {
            $track['_id'] = new ObjectID($track['_id']['$oid']);
            $track['album']['_id'] = new ObjectID($track['album']['_id']['$oid']);
            $track['album']['artist']['_id'] = new ObjectID($track['album']['artist']['_id']['$oid']);
            if(isset($track['album']['header'])) {
                $track['album']['header']['_id'] = new ObjectID($track['album']['header']['_id']['$oid']);
                $track['album']['header']['artist']['_id'] = new ObjectID($track['album']['header']['artist']['_id']['$oid']);
            }

            if(isset($track['album']['genres'])) {
                foreach ($track['album']['genres'] as &$genre) {
                    $genre['_id'] = new ObjectID($genre['_id']['$oid']);

                    unset($genre);
                }
            }

            unset($track);
        }

        if(!isset($input['tracks']) || count($input['tracks']) <= 0)
            return $this->echoError(['error' => 'Playlist must have some tracks.']);

        $this->dm->selectCollection(PLAYLISTS)->updateOne(
            ['_id' => new ObjectID($_id)],
            [
                '$set' => ['tracks' => $input['tracks']]
            ]
        );

        return $this->echoNormal([]);
    }

    protected function updateAllPlaylists()
    {
        $input = getJSONInput();

        foreach($input['playlists'] as &$playlist)
        {
            $playlist['_id'] = new ObjectID($playlist['_id']['$oid']);
            foreach($playlist['tracks'] as &$track)
            {
                $track['_id'] = new ObjectID($track['_id']['$oid']);
                $track['album']['_id'] = new ObjectID($track['album']['_id']['$oid']);
                $track['album']['artist']['_id'] = new ObjectID($track['album']['artist']['_id']['$oid']);
            }
        }

        $this->dm->selectCollection(PLAYLISTS)->deleteMany([]);

        $this->dm->selectCollection(PLAYLISTS)->insertMany($input['playlists']);

        return $this->echoNormal([]);
    }

    protected function get()
    {
        $playlists = $this->dm->selectCollection(PLAYLISTS)->find([])->toArray();

        return $this->echoNormal($playlists);
    }

    protected function getTracks($_id)
    {
        $playlist = (array) $this->dm->selectCollection(PLAYLISTS)->findOne(['_id' => new ObjectID($_id)]);

        return $this->echoNormal($playlist['tracks']);
    }

    protected function delete($_id)
    {
        $playlist = (array) $this->dm->selectCollection(PLAYLISTS)->findOne(['_id' => new ObjectID($_id)]);

        return $this->render('admin.playlists.delete', ['playlist' => $playlist]);
    }

    protected function deletePost($_id)
    {
        $this->dm->selectCollection(PLAYLISTS)->deleteOne(['_id' => new ObjectID($_id)]);

        return redirect("/$this->_admin_url/playlists");
    }
}