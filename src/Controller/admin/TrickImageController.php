<?php


namespace App\Controller\admin;


use App\Entity\Trick;
use App\Entity\TrickImage;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/trick")
 */
class TrickImageController  extends AbstractController
{
    /**
     * @Route("/add/image/{id}", name="admin_trick_add_image", methods={"POST"})
     * @param Trick $trick
     * @param Request $request
     * @param UploaderHelper $uploaderHelper
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function uploadTrickImageCollection(Trick $trick, Request $request, UploaderHelper $uploaderHelper, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('trickImage');

        $violations = $validator->validate(
            $uploadedFile,
            new File([
                'maxSize' => '5M',
                'mimeTypes' => [
                    'image/*',
                ]
            ])
        );

        if ($violations->count() > 0) {
            return $this->json($violations, 400);
        }

        $filename = $uploaderHelper->uploadTrickImageCollection($uploadedFile);

        $trickImage = new TrickImage($trick);

        $trickImage->setFilename($filename);
        $trickImage->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $filename);
        $trickImage->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');

        $entityManager->persist($trickImage);
        $entityManager->flush();

        return $this->json(
            $trickImage,
            201,
            [],
            [
                'groups' => ['main']
            ]
        );
    }

    /**
     * @Route("/list/images/{id}", methods="GET", name="admin_trick_list_images")
     * @param Trick $trick
     * @return JsonResponse
     */
    public function getTrickImage(Trick $trick): JsonResponse
    {
        return $this->json(
            $trick->getTrickImages(),
            200,
            [],
            [
                'groups' => ['main']
            ]
        );
    }

    /**
     * @Route("/image/{id}", name="admin_trick_delete_image", methods={"DELETE"})
     * @param TrickImage $trickImage
     * @param UploaderHelper $uploaderHelper
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function deleteTrickImage(TrickImage $trickImage, UploaderHelper $uploaderHelper, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($trickImage);
        $entityManager->flush();

        $uploaderHelper->deleteFile($trickImage->getFilePath());
        return new Response(null, 204);
    }

}