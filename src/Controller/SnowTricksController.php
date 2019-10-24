<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SnowTricksController extends AbstractController
{
    /**
     * @Route("/", name="trick_home")
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();
        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }


    /**
     * @Route("/trick/show/{id}", name="trick_show")
     * @param Trick $trick
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function show(Trick $trick, Request $request, ObjectManager $manager): Response
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

        return $this->render('home/show.html.twig', [
            'trick' => $trick,
            'formComment' => $form->createView(),
        ]);
    }

  }