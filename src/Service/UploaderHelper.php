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
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME).'-'.uniqid().'.'.$file->guessExtension();

        $stream = fopen($file->getPathname(), 'r');
        $result = $this->filesystem->writeStream(
            self::TRICK_IMAGE.'/'.$newFilename,
            $stream
        );

        if ($result === false) {
            throw new \Exception(sprintf('Impossible d\'enregistrer le fichier télécharger "%s" !', $newFilename));
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        if ($existingFilename) {
            try {
                $result = $this->filesystem->delete(self::TRICK_IMAGE . '/' . $existingFilename);

                if ($result === false) {
                    throw new \Exception(sprintf('Impossible de supprimer l\'ancien fichier "%s" !', $existingFilename));
                }
            } catch (FileNotFoundException $e) {
                $this->logger->alert(sprintf('L\'ancien fichier "%s" est introuvable pour être supprimé !', $existingFilename));
            }
        }

        return  $newFilename;
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().$this->publicAssetBaseUrl.'/'.$path;
    }

}