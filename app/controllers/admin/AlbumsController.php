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

class AlbumsController extends Controller
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
        $list = $this->dm->selectCollection(ALBUMS)->find([])->toArray();

        return $this->render('admin.albums.index', ['opr' => getOpR(), 'list' => $list]);
    }

    protected function new()
    {
        $artists = $this->dm->selectCollection(ARTISTS)->find([])->toArray();
        return $this->render('admin.albums.new', ['opr' => getOpR(), 'artists' => $artists]);
    }

    protected function newPost()
    {
        $form = $this->getRequest()->request;

        $title = $form->get('title');
        $artist = $form->get('artist');

        if($artist == null)
        {
            setOpR(false, 'Artist can not be null');
            return redirect("/$this->_admin_url/albums/new");
        }

        if($title == null)
        {
            setOpR(false, 'Name can not be null');
            return redirect("/$this->_admin_url/albums/new");
        }

        $artist = $this->dm->selectCollection(ARTISTS)->findOne(['_id' => new ObjectID($artist)]);

        $entries = [
            'title' => $title,
            'artist' => $artist,
            'tracks' => [],
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime(),
        ];

        $this->dm->selectCollection(ALBUMS)->insertOne($entries);

        return redirect("/$this->_admin_url/albums");
    }

    protected function edit($_id)
    {
        $album = $this->dm->selectCollection(ALBUMS)->findOne(['_id' => new ObjectID($_id)]);
        $artists = $this->dm->selectCollection(ARTISTS)->find([])->toArray();
        $genres = $this->dm->selectCollection(GENRES)->find([])->toArray();

        $genresTitles = [];
        if(isset($album['genres'])) {
            foreach ($album['genres'] as $genre)
            {
                $genresTitles[] = $genre['title'];
            }
        }

        return $this->render('admin.albums.edit', ['opr' => getOpR(), 'album' => $album, 'artists' => $artists, 'genres' => $genres, 'genresTitles' => $genresTitles]);
    }

    protected function editPost($_id)
    {
        $form = $this->getRequest()->request->all();

        try {
            \AlbumsService::updateAlbum($_id, $form);

            setOpR(true, 'Success');
        } catch (\Exception $e) {
            setOpR(false, $e->getMessage() . ' ' . $e->getFile() . ' on line: #' . $e->getLine());
        }

        return redirect("/$this->_admin_url/albums/edit/" . new ObjectID($_id));
    }

    protected function removeImagePost($_id)
    {
        try {
            \AlbumsService::removeAlbumImage($_id);

            setOpR(true, 'Success.');
        } catch (\Exception $e) {
            setOpR(false, $e->getMessage());
        }

        return $this->echoNormal([]);
    }

    protected function getAlbums()
    {
        $albums = $this->dm->selectCollection(ALBUMS)->find([])->toArray();

        return $this->echoNormal($albums);
    }

    protected function getTracks($_id)
    {
        $album = $this->dm->selectCollection(ALBUMS)->findOne(['_id' => new ObjectID($_id)]);

        return $this->echoNormal($album['tracks']);
    }

    protected function uploadTracks($_id)
    {
        $album = $this->dm->selectCollection(ALBUMS)->findOne(['_id' => new ObjectID($_id)]);

        return $this->render('admin.albums.upload-tracks', ['opr' => getOpR(), 'album' => $album]);
    }

    protected function uploadTrackPost($_id)
    {
        $form = $this->getRequest()->query->all();
        try {
            \AlbumsService::addUploadedTrackToAlbum($_id, $form);
            \HeadersService::refreshHeaderForAlbum($_id);
        } catch (\Exception $e) {

        }
    }

    protected function updateAlreadyUploadedTracks($_id)
    {
        $input = getJSONInput();

        if(!isset($input['tracks']))
        {
            return $this->echoError(['error' => 'No tracks found.']);
        }

        foreach($input['tracks'] as &$track)
        {
            $track['_id'] = new ObjectID($track['_id']['$oid']);
            if(isset($track['album'])) {
                $track['album']['_id'] = new ObjectID($track['album']['_id']['$oid']);
                if(isset($track['album']['artist'])) {
                    $track['album']['artist']['_id'] = new ObjectID($track['album']['artist']['_id']['$oid']);
                }
            }
        }

        $existingAlbum = (array) $this->dm->selectCollection(ALBUMS)->findOne(['_id' => new ObjectID($_id)]);

        $trashTracks = [];
        foreach ($existingAlbum['tracks'] as $existingTrack)
        {
            $found = false;
            foreach ($input['tracks'] as $newTrack)
            {
                if($newTrack['_id'] == $existingTrack['_id'])
                {
                    $found = true;
                }
            }

            if(!$found) {
                $trashTracks[] = $existingTrack;
            }
        }

        // update the album tracks
        $this->dm->selectCollection(ALBUMS)->updateOne(
            ['_id' => new ObjectID($_id)],
            ['$set' => ['tracks' => $input['tracks']]]
        );

        // remove the trash tracks in playlists and also them from Tracks collection
        foreach ($trashTracks as $track)
        {
            if(isset($track['album']))
                continue;

            $this->dm->selectCollection(PLAYLISTS)->updateMany(
                ['tracks._id' => new ObjectID($track['_id'])],
                ['$pull' => ['tracks' => ['_id' => new ObjectID($track['_id'])]]]
            );

            $this->dm->selectCollection(TRACKS)->deleteOne(['_id' => new ObjectID($track['_id'])]);
        }

        return $this->echoNormal([]);
    }

    protected function singleTrack($_albumId, $_trackId)
    {
        $existingAlbum = (array) $this->dm->selectCollection(ALBUMS)->findOne(['_id' => new ObjectID($_albumId)]);

        foreach ($existingAlbum['tracks'] as $t)
        {
            if($t['_id'] == new ObjectID($_trackId))
            {
                $track = $t;
            }
        }

        if(!isset($track))
            notFound();

        return $this->render('admin.albums.single-track', ['opr' => getOpR(), 'albumId' => $_albumId, 'track' => $track]);
    }

    protected function singleTrackPost($_albumId, $_trackId)
    {
        $form = $this->getRequest()->request->all();

        try {
            $track = \AlbumsService::updateSingleTrackInAlbum($_albumId, $_trackId, $form);
            \PlaylistsService::updateSingleTrackInAllPlaylists($track);
            \HeadersService::refreshHeaderForAlbum($_albumId);

            setOpR(true, 'Success');
        } catch (\Exception $e) {
            setOpR(false, $e->getMessage());
        }

        return redirect("/$this->_admin_url/albums/single-track/$_albumId/$_trackId");
    }

    protected function delete($_id)
    {
        $album = (array) $this->dm->selectCollection(ALBUMS)->findOne(['_id' => new ObjectID($_id)]);

        return $this->render('admin.albums.delete', ['album' => $album]);
    }

    protected function deletePost($_id)
    {
        $this->dm->selectCollection(ALBUMS)->deleteOne(['_id' => new ObjectID($_id)]);

        $this->dm->selectCollection(TRACKS)->deleteMany(
            ['album._id' => new ObjectID($_id)]
        );

        $this->dm->selectCollection(HEADERS)->deleteOne(
            ['type' => 'album', 'album._id' => new ObjectID($_id)]
        );

        $playlistsWithTracksFromThisAlbum = $this->dm->selectCollection(PLAYLISTS)->find(
            ['tracks.album._id' => new ObjectID($_id)]
        )->toArray();

        foreach ($playlistsWithTracksFromThisAlbum as &$playlist) {

            $tracksToKeep = [];

            foreach ($playlist['tracks'] as $track) {
                if((string) $track['album']['_id'] != $_id) {
                    $tracksToKeep[] = $track;
                }

                unset($track);
            }

            $playlist['tracks'] = $tracksToKeep;

            $this->dm->selectCollection(PLAYLISTS)->replaceOne(
                ['_id' => new ObjectID($playlist['_id'])],
                $playlist
            );

            unset($playlist);
        }

        return redirect("/$this->_admin_url/albums");
    }
}