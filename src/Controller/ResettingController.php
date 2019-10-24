<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\Mailer;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Form\ResettingType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/resetting/password")
 */
class ResettingController extends AbstractController
{
    /**
     * @Route("/request", name="request_resetting")
     * @param ObjectManager $manager
     * @param UserRepository $userRepository
     * @param Request $request
     * @param Mailer $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function request(ObjectManager $manager, UserRepository $userRepository, Request $request, Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        // création d'un formulaire "à la volée", afin que l'internaute puisse renseigner son mail
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
                    new NotBlank()
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        $user = $userRepository->findOneByEmail($form->getData()['email']);

            if (!$user) {
                $this->addFlash('warning', 'Aucun Membre du site ne correspond à cet email.');
                return $this->redirectToRoute('request_resetting');
            }

            // création du token
            $user->setToken($tokenGenerator->generateToken());

            // enregistrement de la date de création du token
            $user->setPasswordRequestedAt(new \Datetime());
            $manager->flush();

            $bodyMail = $mailer->createBodyMail('resetting/mail.html.twig', [
                'user' => $user
            ]);

            $mailer->sendMessage('from@email.com', $user->getEmail(), 'Réinitialisation de votre mot de passe', $bodyMail);
            $this->addFlash('success', "Un mail vient de vous être envoyé pour réinitialiser votre mot de passe. Le lien qu'il contient sera valide 10 min.");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('resetting/request.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // si supérieur à 10min, retourne false
    private function isRequestInTime(\Datetime $passwordRequestedAt = null): bool
    {
        if ($passwordRequestedAt === null)
        {
            return false;
        }

        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();

        $daySeconds = 60 * 10;
        $response = $interval > $daySeconds ? false : $response = true;

        return $response;
    }

    /**
     * @Route("/{id}/{token}", name="resetting")
     * @param ObjectManager $manager
     * @param User $user
     * @param $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function resetting(ObjectManager $manager, User $user, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token enregistré en base et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 10 minutes
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ResettingType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès !');

            return $this->redirectToRoute('app_login');

        }

        return $this->render('resetting/index.html.twig', [
            'form' => $form->createView()
        ]);

    }

}
