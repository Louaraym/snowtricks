<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    //const TRICK_IMAGE = 'trick_image';

    /**
     * @var string
     */
    private $uploadsPath;

    public function __construct(string $uploadsPath)
    {
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadTrickImage(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadsPath.'/trick_image';
        $originalFilename = pathinfo( $uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename =$originalFilename.'-'.uniqid().'-'. $uploadedFile->guessExtension();

        $uploadedFile->move(
            $destination,
            $newFilename
        );

        return  $newFilename;
    }

}