<?php


namespace App\Service;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const TRICK_IMAGE = 'trick_image';

    const TRICK_IMAGE_COLLECTION = 'trick_image_collection';

    /**
     * @var string
     */
    private $filesystem;

    private $requestStackContext;

    private $logger;

    private $publicAssetBaseUrl;

    public function __construct(FilesystemInterface $publicUploadsFilesystem, RequestStackContext $requestStackContext,  LoggerInterface $logger, string $uploadedAssetsBaseUrl)
    {
        $this->filesystem = $publicUploadsFilesystem;
        $this->requestStackContext = $requestStackContext;
        $this->logger = $logger;
        $this->publicAssetBaseUrl = $uploadedAssetsBaseUrl;
    }

    public function uploadTrickImage(File $file, ?string $existingFilename): string
    {
        $newFilename = $this->uploadFile($file, self::TRICK_IMAGE);
        if ($existingFilename) {
            try {
                $result = $this->filesystem->delete(self::TRICK_IMAGE.'/'.$existingFilename);
                if ($result === false) {
                    throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFilename));
                }
            } catch (FileNotFoundException $e) {
                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            }
        }
        return $newFilename;
    }

    public function uploadTrickImageCollection(File $file): string
    {
        return $this->uploadFile($file, self::TRICK_IMAGE_COLLECTION);
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().$this->publicAssetBaseUrl.'/'.$path;
    }

    private function uploadFile(File $file, string $directory): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }
        $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME).'-'.uniqid().'.'.$file->guessExtension();
        $filesystem = $this->filesystem;
        $stream = fopen($file->getPathname(), 'r');
        $result = $filesystem->writeStream(
            $directory.'/'.$newFilename,
            $stream
        );
        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        }
        if (is_resource($stream)) {
            fclose($stream);
        }
        return $newFilename;
    }

}