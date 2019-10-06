<?php


namespace App\Service;

use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const TRICK_IMAGE = 'trick_image';

    /**
     * @var string
     */
    private $uploadsPath;

    private $requestStackContext;

    public function __construct(string $uploadsPath, RequestStackContext $requestStackContext)
    {
        $this->uploadsPath = $uploadsPath;
        $this->requestStackContext = $requestStackContext;
    }

    public function uploadTrickImage(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadsPath.'/'.self::TRICK_IMAGE;
        $originalFilename = pathinfo( $uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename =$originalFilename.'-'.uniqid().'-'. $uploadedFile->guessExtension();

        $uploadedFile->move(
            $destination,
            $newFilename
        );

        return  $newFilename;
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().'/uploads/'.$path;
    }

}