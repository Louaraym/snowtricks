<?php


namespace App\Service;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const TRICK_IMAGE = 'trick_image';

    /**
     * @var string
     */
    private $filesystem;

    private $requestStackContext;

    public function __construct(FilesystemInterface $publicUploadsFilesystem, RequestStackContext $requestStackContext)
    {
        $this->filesystem = $publicUploadsFilesystem;
        $this->requestStackContext = $requestStackContext;
    }

    public function uploadTrickImage(File $file): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME).'-'.uniqid().'.'.$file->guessExtension();

        $this->filesystem->write(
            self::TRICK_IMAGE.'/'.$newFilename,
            file_get_contents($file->getPathname())
        );

        return  $newFilename;
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().'/uploads/'.$path;
    }

}