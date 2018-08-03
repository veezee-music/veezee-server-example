<?php

namespace App\Services;

use MongoDB\BSON\ObjectId;

class HeadersService extends BaseService
{
    static function createOrUpdateHeaderForAlbum(array $album, ?string $headerImageName, bool $active): ?array
    {
        $header = [
            'active' => $active,
            'title' => $album['title'],
            'artist' => $album['artist'],
            'type' => 'album',
            'updatedAt' => new \DateTime(),
            'album' => $album
        ];

        $existingHeader = (array) self::getDM()->selectCollection(HEADERS)->findOne(
            ['album._id' => new ObjectID($album['_id'])]
        );

        if(count($existingHeader) <= 0 && $headerImageName == null) {
            return null;
        }

        if($headerImageName != null) {
            $header['image'] = $headerImageName;
        } else {
            $header['image'] = $existingHeader['image'];
        }

        if($existingHeader != null && count($existingHeader) >= 0) {
            $existingHeader = array_replace($existingHeader, $header);

            self::getDM()->selectCollection(HEADERS)->replaceOne(
                ['_id' => new ObjectID($existingHeader['_id'])],
                $existingHeader
            );

            $header['_id'] = $existingHeader['_id'];
        } else {
            $res = self::getDM()->selectCollection(HEADERS)->insertOne($header);

            $header['_id'] = $res->getInsertedId();
        }

        return $header;
    }

    static function addTrackToHeader($headerId, $trackId)
    {
        $header = (array) self::getDM()->selectCollection(HEADERS)->findOne(
            ['_id' => new ObjectID($headerId)]
        );

        $track = (array) self::getDM()->selectCollection(TRACKS)->findOne(
            ['_id' => new ObjectId($trackId)]
        );

        if((string) $header['album']['_id'] != (string) $track['album']['_id']) {
            return ['error' => 'Track can only be added to a header for the same album.'];
        }

        unset($track['album']);

        foreach ($header['album']['tracks'] as $track)
        {
            if((string) $track['_id'] == $trackId) {
                return ['error' => 'Track already added to header.'];
            }
        }

        $header['album']['tracks'][] = $track;

        self::getDM()->selectCollection(HEADERS)->replaceOne(
            ['_id' => new ObjectId($headerId)],
            $header
        );

        return [];
    }

    static function refreshHeaderForAlbum($albumId)
    {
        $header = (array) self::getDM()->selectCollection(HEADERS)->findOne(
            ['album._id' => new ObjectID($albumId)]
        );

        if(count($header) <= 0) {
            return [];
        }

        $album = (array) self::getDM()->selectCollection(ALBUMS)->findOne(
            ['_id' => new ObjectId($albumId)]
        );

        if((string) $header['album']['_id'] != (string) $album['_id']) {
            return ['error' => 'Header must be created for the passed album.'];
        }

        unset($album['header']);
        $header['album'] = $album;

        self::getDM()->selectCollection(HEADERS)->replaceOne(
            ['_id' => new ObjectId($header['_id'])],
            $header
        );

        return [];
    }
}