<?php

namespace App\Controller\admin;

use App\Form\AvatarUpdateType;
use App\Service\UploaderHelper;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAccountUpdateController extends AbstractController
{
    /**
     * @Route("/user/account/update/{user}", name="user_account_update")
     * @param UploaderHelper $uploaderHelper
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function index(UploaderHelper $uploaderHelper, Request $request, ObjectManager $manager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AvatarUpdateType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $uploadedFile = $form['avatarFile']->getData();

            if ($uploadedFile){
                $newFilename = $uploaderHelper->uploadTrickImage($uploadedFile, $user->getAvatarFilename());
                $user->setAvatarFilename($newFilename);
            }

            $manager->flush();
            $this->addFlash('success', 'Votre modification a été effectuée avec succès !');
            return  $this->redirectToRoute('account', ['user' => $this->getUser()]);
        }

        return $this->render('user_account_update/index.html.twig', [
            'avatarUpdateForm' =>$form->createView(),
        ]);
    }
}
