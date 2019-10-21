<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Service\UploaderHelper;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("TRICK_CREATE")
     * @param UploaderHelper $uploaderHelper
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     * @throws Exception
     */
    public function createTrick(UploaderHelper $uploaderHelper, Request $request, ObjectManager $manager): Response
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
            return $this->redirectToRoute('snowtricks_home');
        }

        return $this->render('snow_trick_admin/createTrick.html.twig', [
            'formTrick' => $form->createView(),
        ]);
    }

    /**
     * @Route("/snowtricks/{id}", name="trick_show")
     *
     * @param Trick $trick
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function showTrick(Trick $trick, Request $request, ObjectManager $manager): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $comment->setTrick($trick)
                    ->setAuthorName($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);

        }

        return $this->render('snow_trick/showTrick.html.twig', [
            'trick' => $trick,
            'formComment' => $form->createView(),
        ]);
    }

    /**
     * @Route("/snowtricks/edit/{id}", name="trick_edit", methods="GET|POST")
     * @IsGranted("TRICK_EDIT", subject="trick")
     * @param UploaderHelper $uploaderHelper
     * @param Trick $trick
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     * @throws Exception
     * @var UploadedFile $uploadedFile
     */
    public function edit(UploaderHelper $uploaderHelper,Trick $trick, Request $request, ObjectManager $manager): Response
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
            return  $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        return $this->render('snow_trick_admin/editTrick.html.twig', [
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
