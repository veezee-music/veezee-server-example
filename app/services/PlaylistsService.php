<?php

namespace App\Services;

use MongoDB\BSON\ObjectId;
use MongoDB;

class PlaylistsService extends BaseService
{
    static function updateSingleTrackInAllPlaylists($track)
    {
        $existingPlaylists = self::getDM()->selectCollection(PLAYLISTS)->find(['tracks._id' => new ObjectID($track['_id'])])->toArray();
        foreach ($existingPlaylists as &$playlist)
        {
            foreach ($playlist['tracks'] as &$playlistTrack)
            {
                if((string) $playlistTrack['_id'] == (string) $track['_id']) {
                    $playlistTrack = $track;

                    break;
                }

                unset($playlistTrack);
            }

            self::getDM()->selectCollection(PLAYLISTS)->replaceOne(
                ['_id' => new ObjectID($playlist['_id'])],
                $playlist
            );

            unset($playlist);
        }
    }

    static function removePlaylistImage($playlistId)
    {
        $playlist = self::getDM()->selectCollection(PLAYLISTS)->findOne(['_id' => new ObjectId($playlistId)]);

        if(isset($playlist['image'])) {
            $playlist['image'] = null;
        }

        if(isset($playlist['colors'])) {
            $playlist['colors'] = null;
        }

        $playlist['updatedAt'] = new \DateTime();

        self::getDM()->selectCollection(PLAYLISTS)->findOneAndUpdate(
            ['_id' => new ObjectID($playlistId)],
            ['$set' => $playlist],
            ['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );
    }
}