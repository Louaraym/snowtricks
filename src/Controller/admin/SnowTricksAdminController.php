<?php

namespace App\Controller\admin;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/trick")
 */
class SnowTricksAdminController extends AbstractController
{
    /**
     * @Route("/new", name="admin_trick_new")
     * @IsGranted("ROLE_USER")
     * @param UploaderHelper $uploaderHelper
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function new(UploaderHelper $uploaderHelper, Request $request, EntityManagerInterface $manager): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $trick->setAuthor($this->getUser());

            $uploadedFile = $form['imageFile']->getData();

            if ($uploadedFile){
                $newFilename = $uploaderHelper->uploadTrickImage($uploadedFile, $trick->getImageFilename());
                $trick->setImageFilename($newFilename);
            }

            $manager->persist($trick);
            $manager->flush();
            $this->addFlash('success', 'Votre Ajout a été effectué avec succès !');
            return $this->redirectToRoute('trick_home');
        }

        return $this->render('admin/new.html.twig', [
            'formTrick' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="admin_trick_edit", methods="GET|POST")
     * @IsGranted("TRICK_MANAGE", subject="trick")
     * @param UploaderHelper $uploaderHelper
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws Exception
     */
    public function edit(UploaderHelper $uploaderHelper,Trick $trick, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $uploadedFile = $form['imageFile']->getData();

            if ($uploadedFile){
                $newFilename = $uploaderHelper->uploadTrickImage($uploadedFile, $trick->getImageFilename());
                $trick->setImageFilename($newFilename);
            }

            $trick->setUpdatedAt(new \DateTime());

            $manager->flush();
            $this->addFlash('success', 'Votre modification a été effectuée avec succès !');
            return  $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug(),'id' => $trick->getId()]);
        }

        return $this->render('admin/edit.html.twig', [
            'trick' => $trick,
            'formTrick' =>$form->createView(),
        ]);
    }


    /**
     * @Route("/delete/{id}", name="admin_trick_delete", methods= "DELETE")
     * @IsGranted("TRICK_MANAGE", subject="trick")
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Trick $trick, Request $request, EntityManagerInterface $manager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token'))){
            $manager->remove($trick);
            $manager->flush();
            $this->addFlash('success', 'Votre suppression a été effectuée avec succès !');
        }

        return  $this->redirectToRoute('trick_home');
    }

}
