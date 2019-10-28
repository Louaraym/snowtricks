<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmAccountController extends AbstractController
{
    /**
     * @Route("/account/confirm/{token}/{username}", name="confirm_account")
     * @param User $user
     * @param ObjectManager $manager
     * @param $token
     * @return Response
     */
    public function confirmAccount(User $user, ObjectManager $manager, $token): Response
    {
        $tokenExist = $user->getToken();

        if($token === $tokenExist) {
            $user->setToken(null);
            $user->setEnabled(true);
            $manager->flush();
            $this->addFlash('success', 'Bravo, votre compte a été activé avec succès, vous pouvez vous connecter en toute sécurité !');
            return $this->redirectToRoute('trick_home');
        }

        return $this->render('confirm_account/token_expired.html.twig');
    }

}
