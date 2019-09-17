<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SnowTrickController extends AbstractController
{
    /**
     * @Route("/snow/trick", name="snow_trick")
     */
    public function index(): Response
    {
        return $this->render('snow_trick/index.html.twig', [
            'controller_name' => 'SnowTrickController',
        ]);
    }
}
