<?php

use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Extension;
use Upload\Validation\Size;

class AssetsService extends BaseService
{
    static function saveUploadedArtwork($paramName = 'image'): ?string
    {
        if(!isset($_FILES[$paramName]) || !file_exists($_FILES[$paramName]['tmp_name']) || !is_uploaded_file($_FILES[$paramName]['tmp_name']))
        {
            return null;
        }
        $storage = new FileSystem('content/images');
        // \Upload\File($postedFileInputName, \Upload\Storage\FileSystem $storage)
        $image = new File($paramName, $storage);
        // Rename the file on upload
        $image->setName(uniqid() . time());
        // Validate file upload
        $image->addValidations([
            new Extension(['png', 'jpg']),
            // Ensure file is no larger than 5M (use "B", "K", M", or "G")
            new Size('500K')
        ]);
        $dimension = $image->getDimensions();
        if($dimension['width'] !=  $dimension['height'])
        {
            $image->addError('Image must be a square');
        }
        try
        {
            $image->upload();

            return $image->getNameWithExtension();
        }
        catch (\Exception $e) {}
        if($image->getErrors() != null)
        {
            throw new \Exception(flattenStringArray($image->getErrors()));
        }
    }

    static function saveUploadedHeaderImage($paramName = 'headerImage'): ?string
    {
        if(!isset($_FILES[$paramName]) || !file_exists($_FILES[$paramName]['tmp_name']) || !is_uploaded_file($_FILES[$paramName]['tmp_name']))
        {
            return null;
        }

        $storage = new FileSystem('content/images');
        // \Upload\File($postedFileInputName, \Upload\Storage\FileSystem $storage)
        $headerImage = new File($paramName, $storage);
        // Rename the file on upload
        $headerImage->setName(uniqid() . time());
        // Validate file upload
        $headerImage->addValidations([
            new Extension(['png', 'jpg']),
            // Ensure file is no larger than 5M (use "B", "K", M", or "G")
            new Size('500K')
        ]);
//        $dimension = $headerImage->getDimensions();
//        if($dimension['width'] !=  $dimension['height'])
//        {
//            $headerImage->addError('Image must be a square');
//        }
        try
        {
            $headerImage->upload();

            return $headerImage->getNameWithExtension();
        }
        catch (\Exception $e) {}
        if($headerImage->getErrors() != null)
        {
            throw new \Exception(flattenStringArray($headerImage->getErrors()));
        }
    }
}