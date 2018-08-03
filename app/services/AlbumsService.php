<?php

namespace App\Services;

use MongoDB;
use MongoDB\BSON\ObjectId;
use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Extension;
use Upload\Validation\Size;

class AlbumsService extends BaseService
{
    static function addUploadedTrackToAlbum($albumId, $input)
    {
        $publishUpdate = $input['publishUpdate'];

        if(!isset($_FILES['track']) || !file_exists($_FILES['track']['tmp_name']) || !is_uploaded_file($_FILES['track']['tmp_name']))
        {
            throw new \Exception('File does not exist.');
        }

        $album = self::getDM()->selectCollection(ALBUMS)->findOne(
            ['_id' => new ObjectID($albumId)],
            ['projection' => ['tracks' => 0]] // exclude properties from the result
        );

        if(!file_exists("content/music/albums/$albumId"))
        {
            mkdir("content/music/albums/$albumId");
        }

        $trackId = new ObjectID();

        $storage = new FileSystem("content/music/albums/$albumId");
        // \Upload\File($postedFileInputName, \Upload\Storage\FileSystem $storage)
        $file = new File('track', $storage);
        $title = $file->getName();
        // Rename the file on upload
        $file->setName((string) $trackId);
        // Validate file upload
        $file->addValidations([
            new Extension(['mp3', 'm4a']),
            // Ensure file is no larger than 5M (use "B", "K", M", or "G")
            new Size('20M')
        ]);

        $track = [
            '_id' => $trackId,
            'title' => $title,
            'fileName' => $trackId . '.' . $file->getExtension(),
            'originalFileName' => $_FILES['track']['name'],
            'createdAt' => new \DateTime(),
            'updatedAt' => new \DateTime()
        ];

        if($publishUpdate != null && ($publishUpdate == false || $publishUpdate == "false")) {
            $track['publishUpdate'] = false;
        }

        if(isset($album['image']) && $album['image'] != null) {
            $track['image'] = $album['image'];
            if(isset($album['colors']) && $album['colors'] != null) {
                $track['colors'] = $album['colors'];
            }
        }

        try
        {
            $file->upload();

            self::getDM()->selectCollection(ALBUMS)->updateOne([
                '_id' => new ObjectID($albumId)
            ], [
                '$push' => [
                    'tracks' => $track
                ]
            ]);

            $track['album'] = $album;
            self::getDM()->selectCollection(TRACKS)->insertOne($track);
        }
        catch (\Exception $e) {}

        if($file->getErrors() != null)
        {
            throw new \Exception(flattenStringArray($file->getErrors()));
        }

        return [];
    }

    static function updateSingleTrackInAlbum($albumId, $trackId, $input): array
    {
        $title = $input['title'];
        $readImageFromAlbum = $input['readImageFromAlbum'] ?? null;
        $publishUpdate = $input['publishUpdate'];

        $primaryColor = $input['primaryColor'];
        $accentColor = $input['accentColor'];

        if($title == null)
        {
            throw new \Exception('Title can not be null');
        }

        if(isset($_FILES['image']) && file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $imageFileName = AssetsService::saveUploadedArtwork('image');
        }

//        if($readImageFromAlbum != null) {
//            goto SKIP_IMAGE_UPLOAD;
//        }
//
//        if(!isset($_FILES['image']) || !file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name']))
//        {
//            goto SKIP_IMAGE_UPLOAD;
//        }
//        $storage = new FileSystem('content/images');
//        // \Upload\File($postedFileInputName, \Upload\Storage\FileSystem $storage)
//        $image = new File('image', $storage);
//        // Rename the file on upload
//        $image->setName(uniqid() . time());
//        // Validate file upload
//        $image->addValidations([
//            new Extension(['png', 'jpg']),
//            // Ensure file is no larger than 5M (use "B", "K", M", or "G")
//            new Size('500K')
//        ]);
//        $dimension = $image->getDimensions();
//        if($dimension['width'] !=  $dimension['height'])
//        {
//            $image->addError('Image must be a square');
//        }
//        try
//        {
//            $image->upload();
//        }
//        catch (\Exception $e) {}
//        if($image->getErrors() != null)
//        {
//            throw new \Exception(flattenStringArray($image->getErrors()));
//        }
//        SKIP_IMAGE_UPLOAD:

        $entries = [
            'title' => $title
        ];

        $existingAlbum = self::getDM()->selectCollection(ALBUMS)->findOne(
            ['_id' => new ObjectID($albumId)]
        );

        $existingTrack = self::getDM()->selectCollection(TRACKS)->findOne(
            ['_id' => new ObjectID($trackId)]
        );

        if($publishUpdate == null || $publishUpdate == false || $publishUpdate == "false") {
            $entries['publishUpdate'] = false;
        } else if($publishUpdate != null) {
            $entries['publishUpdate'] = true;
        }

        if(isset($imageFileName) && $readImageFromAlbum == null) {
            $entries['image'] = $imageFileName;
        }

        $entries['colors'] = [
            'primaryColor' => $primaryColor,
            'accentColor' => $accentColor
        ];

        if($readImageFromAlbum != null && isset($existingAlbum['image'])) {
            $entries['image'] = $existingAlbum['image'];
            if(isset($existingAlbum['colors'])) {
                $entries['colors'] = $existingAlbum['colors'];
            } else {
                $entries['colors'] = null;
            }
        }

        foreach ($existingAlbum['tracks'] as &$track)
        {
            if($track['_id'] == new ObjectID($trackId)) {
                if(isset($entries['title'])) {
                    $track['title'] = $entries['title'];
                }
                if(isset($entries['publishUpdate'])) {
                    $track['publishUpdate'] = $entries['publishUpdate'];
                }
                if(isset($entries['image'])) {
                    $track['image'] = $entries['image'];
                }
                if(isset($entries['colors'])) {
                    $track['colors'] = $entries['colors'];
                }
                $track['updatedAt'] = new \DateTime();

                break;
            }

            unset($track);
        }

        self::getDM()->selectCollection(ALBUMS)->replaceOne(
            ['_id' => new ObjectID($albumId)],
            $existingAlbum
        );

        // update tracks
        $newTrack = self::getDM()->selectCollection(TRACKS)->findOneAndUpdate(
            ['_id' => new ObjectID($trackId)],
            ['$set' => $entries],
            ['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );

        return $newTrack;
    }

    static function updateAlbum($albumId, $input)
    {
        $existingAlbum = (array) self::getDM()->selectCollection(ALBUMS)->findOne(
            ['_id' => new ObjectID($albumId)]
        );

        if($existingAlbum == null || count($existingAlbum) <= 0) {
            throw new \Exception('Album does not exist');
        }

        if($existingAlbum['artist'] == null) {
            throw new \Exception('Artist can not be null');
        }

        if($existingAlbum['title'] == null) {
            throw new \Exception('Title can not be null');
        }

        $existingAlbum['title'] = $input['title'];
        $existingAlbum['artist'] = self::getDM()->selectCollection(ARTISTS)->findOne(['_id' => new ObjectID($input['artist'])]);
        $existingAlbum['publishUpdate'] = $input['publishUpdate'] ?? null;
        $existingAlbum['updatedAt'] = new \DateTime();
        $oldImageFileName = $existingAlbum['image'] ?? null;
        $oldColors = $existingAlbum['colors'] ?? null;

        $headerActive = $input['headerActive'] ?? null;
        $input['genres'] = $input['genres'] ?? [];

        array_unshift($input['genres'], $input['mainGenre']);

        $primaryColor = $input['primaryColor'] ?? null;
        $accentColor = $input['accentColor'] ?? null;

        $existingAlbum['genres'] = [];
        foreach ($input['genres'] as $genre)
        {
            $genreInDb = (array) self::getDM()->selectCollection(GENRES)->findOne(['_id' => new ObjectID($genre)]);

            $existingAlbum['genres'][] = $genreInDb;
        }

        if(isset($_FILES['image']) && file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $imageFileName = AssetsService::saveUploadedArtwork('image');
        }

        if(isset($_FILES['headerImage']) && file_exists($_FILES['headerImage']['tmp_name']) && is_uploaded_file($_FILES['headerImage']['tmp_name']))
        {
            $headerImageFileName = AssetsService::saveUploadedHeaderImage('headerImage');
        }

        if(isset($imageFileName) || (isset($existingAlbum['image']) && $existingAlbum['image'] != null)) {
            $existingAlbum['colors'] = [
                'primaryColor' => $primaryColor,
                'accentColor' => $accentColor
            ];
        }

        if(isset($imageFileName)) {
            $existingAlbum['image'] = $imageFileName;
        }

        if($existingAlbum['publishUpdate'] == null || $existingAlbum['publishUpdate'] == false || $existingAlbum['publishUpdate'] == "false") {
            $existingAlbum['publishUpdate'] = false;
        }
        if(isset($existingAlbum['publishUpdate']) && $existingAlbum['publishUpdate'] != null) {
            $existingAlbum['publishUpdate'] = true;
        }

        $albumWithUpdatedTracks = AlbumsService::updateTracksListWithAlbum($existingAlbum, $oldImageFileName, $oldColors);

        $headerImage = (isset($headerImageFileName) && $headerImageFileName != null) ? $headerImageFileName : null;
        $header = HeadersService::createOrUpdateHeaderForAlbum($albumWithUpdatedTracks, $headerImage, $headerActive != null ? true : false);
        if($header != null) {
            unset($header['album']);
            $albumWithUpdatedTracks['header'] = $header;
        }

        $newAlbum = self::getDM()->selectCollection(ALBUMS)->findOneAndUpdate(
            ['_id' => new ObjectID($albumId)],
            ['$set' => $albumWithUpdatedTracks],
            ['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );

        $newAlbumTracks = $newAlbum['tracks'];
        unset($newAlbum['tracks']);
        foreach ($newAlbumTracks as $track)
        {
            $track['album'] = $newAlbum;
            PlaylistsService::updateSingleTrackInAllPlaylists($track);
        }
    }

    static function removeAlbumImage($albumId)
    {
        $album = self::getDM()->selectCollection(ALBUMS)->findOne(['_id' => new ObjectId($albumId)]);

        $album['image'] = null;
        $album['colors'] = null;

        $album['updatedAt'] = new \DateTime();

        $albumWithUpdatedTracks = AlbumsService::updateTracksListWithAlbum($album);

        if(isset($headerImageFileName) && $headerImageFileName != null) {
            $header = HeadersService::createOrUpdateHeaderForAlbum($album, $headerImageFileName, $album['header']['active']);
            unset($header['album']);
            $albumWithUpdatedTracks['header'] = $header;
        }

        $newAlbum = self::getDM()->selectCollection(ALBUMS)->findOneAndUpdate(
            ['_id' => new ObjectID($albumId)],
            ['$set' => $albumWithUpdatedTracks],
            ['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );

        $newAlbumTracks = $newAlbum['tracks'];
        unset($newAlbum['tracks']);
        foreach ($newAlbumTracks as $track)
        {
            $track['album'] = $newAlbum;
            PlaylistsService::updateSingleTrackInAllPlaylists($track);
        }
    }

    static function updateTracksListWithAlbum(array $album, $oldImageFileName, $oldColors): array
    {
        $albumWithoutTracks = $album;
        unset($albumWithoutTracks['tracks']);
        $resultAlbum = $album;
        $resultAlbum['tracks'] = [];

        foreach ($album['tracks'] as &$track)
        {
            if($album['image'] != $oldImageFileName) {
                // if track doesn't have an image or is using the old image for the album, update it
                if(!isset($track['image']) || ($track['image'] == $oldImageFileName)) {
                    // eligible for image update
                    $track['image'] = $album['image'] ?? null;
                }
            }
            if($album['colors'] != $oldColors) {
                if(!isset($track['colors']) || ($track['colors'] == $oldColors)) {
                    // eligible for image update
                    $track['colors'] = $album['colors'] ?? null;
                }
            }

            $resultAlbum['tracks'][] = $track;

            $track['album'] = $albumWithoutTracks;

            self::getDM()->selectCollection(TRACKS)->replaceOne(
                ['_id' => new ObjectID($track['_id'])],
                $track
            );

            unset($track);
        }

        return $resultAlbum;
    }
}