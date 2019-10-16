<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Service\UploaderHelper;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SnowTrickController extends AbstractController
{
    /**
     * @Route("/", name="snowtricks_home")
     * @param TrickRepository $repo
     * @return Response
     */
    public function index(TrickRepository $repo): Response
    {
        $tricks = $repo->findAll();
        return $this->render('snow_trick/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    /**
     * @Route("/snowtricks/create", name="snowtricks_create")
     * @param UploaderHelper $uploaderHelper
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     * @throws Exception
     * @var UploadedFile $uploadedFile
     */
    public function createTrick(UploaderHelper $uploaderHelper ,Request $request, ObjectManager $manager): Response
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile){
                $newFilename = $uploaderHelper->uploadTrickImage($uploadedFile, $trick->getImageFilename());
                $trick->setImageFilename($newFilename);
            }

            $manager->persist($trick);
            $manager->flush();
            $this->addFlash('success', 'Votre Ajout a été effectué avec succès !');
            return $this->redirectToRoute('snowtricks_home');
        }

        return $this->render('snow_trick/createTrick.html.twig', [
            'formTrick' => $form->createView(),
        ]);
    }

    /**
     * @Route("/snowtricks/{id}", name="trick_show")
     * @param Trick $trick
     * @return Response
     */
    public function showTrick(Trick $trick): Response
    {
        return $this->render('snow_trick/showTrick.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/snowtricks/edit/{id}", name="trick_edit", methods="GET|POST")
     * @param UploaderHelper $uploaderHelper
     * @param Trick $trick
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     * @throws Exception
     * @var UploadedFile $uploadedFile
     */
    public function edit(UploaderHelper $uploaderHelper ,Trick $trick, Request $request, ObjectManager $manager): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile){
                $newFilename = $uploaderHelper->uploadTrickImage($uploadedFile, $trick->getImageFilename());
                $trick->setImageFilename($newFilename);
            }

            $manager->flush();
            $this->addFlash('success', 'Votre modification a été effectuée avec succès !');
            return  $this->redirectToRoute('snowtricks_home');
        }

        return $this->render('snow_trick/editTrick.html.twig', [
            'trick' => $trick,
            'formTrick' =>$form->createView(),
        ]);
    }


    /**
     * @Route("/snowtricks/delete/{id}", name="trick_delete", methods= "DELETE")
     * @param Trick $trick
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Trick $trick, Request $request, ObjectManager $manager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token'))){
            $manager->remove($trick);
            $manager->flush();
            $this->addFlash('success', 'Votre suppression a été effectuée avec succès !');
        }

        return  $this->redirectToRoute('snowtricks_home');
    }

}
