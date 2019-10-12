<?php


namespace App\Controller;


use App\Entity\Trick;
use App\Entity\TrickImage;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TrickImageController  extends AbstractController
{
    /**
     * @Route("/admin/trick/{id}/images", name="admin_trick_add_image", methods={"POST"})
     * @param Trick $trick
     * @param Request $request
     * @param UploaderHelper $uploaderHelper
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return RedirectResponse
     */
    public function uploadTrickImageCollection(Trick $trick, Request $request, UploaderHelper $uploaderHelper, EntityManagerInterface $entityManager, ValidatorInterface $validator)
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
            /** @var ConstraintViolation $violation */
            $violation = $violations[0];
            $this->addFlash('error', $violation->getMessage());
            return $this->redirectToRoute('trick_edit', [
                'id' => $trick->getId(),
            ]);
        }

        $filename = $uploaderHelper->uploadTrickImageCollection($uploadedFile);

        $trickImage = new TrickImage($trick);

        $trickImage->setFilename($filename);
        $trickImage->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $filename);
        $trickImage->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');

        $entityManager->persist($trickImage);
        $entityManager->flush();

        return $this->redirectToRoute('trick_edit', [
            'id' => $trick->getId(),
        ]);
    }

}