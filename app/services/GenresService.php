<?php

use MongoDB\BSON\ObjectId;

class GenresService extends BaseService
{
    static function createGenre($input): array
    {
        if(!isset($input['title']) || $input['title'] == "") {
            throw new \Exception('Title can not be null');
        }

        $entries = [
            'title' => $input['title'],
            'updatedAt' => new \DateTime(),
        ];

        $res = self::getDM()->selectCollection(GENRES)->insertOne($entries);
        $entries['_id'] = $res->getInsertedId();

        return $entries;
    }

    static function updateGenre($genreId, $input): array
    {
        if(!isset($input['title']) || $input['title'] == "") {
            throw new \Exception('Title can not be null');
        }

        $existingGenre = (array) self::getDM()->selectCollection(GENRES)->findOne(['_id' => new ObjectId($genreId)]);

        $existingGenre['title'] = $input['title'];

        if(isset($_FILES['image']) && file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $imageFileName = AssetsService::saveUploadedArtwork('image');
        }

        if(isset($imageFileName)) {
            $existingGenre['image'] = $imageFileName;
        }

        $newGenre = self::getDM()->selectCollection(GENRES)->findOneAndUpdate(
            ['_id' => new ObjectID($genreId)],
            ['$set' => $existingGenre],
            ['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );


        // update albums
        $albums = self::getDM()->selectCollection(ALBUMS)->find(['genres._id' => new ObjectID($genreId)])->toArray();
        foreach ($albums as &$album)
        {
            foreach ($album['genres'] as &$genre)
            {
                if($genre['_id'] == $genreId) {
                    $genre = $newGenre;
                }

                unset($genre);
            }

            self::getDM()->selectCollection(ALBUMS)->replaceOne(
                ['_id' => new ObjectID($album['_id'])],
                $album
            );

            unset($album);
        }

        // update embedded albums in tracks
        $tracks = self::getDM()->selectCollection(TRACKS)->find(['album.genres._id' => new ObjectID($genreId)])->toArray();
        foreach ($tracks as &$track)
        {
            foreach ($track['album']['genres'] as &$genre)
            {
                if($genre['_id'] == $genreId) {
                    $genre = $newGenre;
                }

                unset($genre);
            }

            self::getDM()->selectCollection(TRACKS)->replaceOne(
                ['_id' => new ObjectID($track['_id'])],
                $track
            );

            unset($track);
        }

        // update embedded albums in playlists
        $playlists = self::getDM()->selectCollection(PLAYLISTS)->find(['tracks.album.genres._id' => new ObjectID($genreId)])->toArray();
        foreach ($playlists as &$playlist)
        {
            foreach ($playlist['tracks'] as &$track)
            {
                foreach ($track['album']['genres'] as &$genre)
                {
                    if($genre['_id'] == $genreId) {
                        $genre = $newGenre;
                    }

                    unset($genre);
                }

                unset($track);
            }

            self::getDM()->selectCollection(PLAYLISTS)->replaceOne(
                ['_id' => new ObjectID($playlist['_id'])],
                $playlist
            );

            unset($playlist);
        }

        // update headers
        $headers = self::getDM()->selectCollection(HEADERS)->find(['album.genres._id' => new ObjectId($genreId)])->toArray();
        foreach ($headers as &$header)
        {
            foreach ($header['album']['genres'] as &$genre)
            {
                if($genre['_id'] == $genreId)
                {
                    $genre = $newGenre;
                }

                unset($genre);
            }

            self::getDM()->selectCollection(HEADERS)->replaceOne(
                ['_id' => new ObjectID($header['_id'])],
                $header
            );

            unset($header);
        }


        return $newGenre;
    }
}