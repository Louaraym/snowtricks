<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Service\Mailer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserAuthenticatorController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_registration")
     * @param Mailer $mailer
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @param ObjectManager $manager
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     */
    public function registration(Mailer $mailer, UserPasswordEncoderInterface $encoder, Request $request, ObjectManager $manager, TokenGeneratorInterface $tokenGenerator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setToken($tokenGenerator->generateToken());
            $manager->persist($user);
            $manager->flush();

            $bodyMail = $mailer->createBodyMail('confirm_account/mail.html.twig', [
                'user' => $user
            ]);

            $mailer->sendMessage('from@email.com', $user->getEmail(), 'Activation de votre compte', $bodyMail);
            $this->addFlash('success', 'Votre inscription a bien été prise en compte. Un mail vient de vous être envoyé pour valider votre compte et pouvoir vous connecter !');

            return  $this->redirectToRoute('trick_home');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \RuntimeException('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
