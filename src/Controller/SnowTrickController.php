<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SnowTrickController extends AbstractController
{
    /**
     * @Route("/snowtricks/home", name="snowtricks_home")
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
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     * @throws Exception
     */
    public function createTrick(Request $request, ObjectManager $manager): Response
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($trick);
            $manager->flush();

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
}
