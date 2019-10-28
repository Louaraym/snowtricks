<?php

namespace App\Controller\admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentAdminController
 * @package App\Controller\admin
 * @IsGranted("ROLE_USER")
 */
class CommentAdminController extends AbstractController
{
    /**
     * @Route("/admin/comment", name="comment_admin")
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function index(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findAll();

        return $this->render('comment_admin/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("admin/comment/{id}", name="comment_admin_delete", methods= "DELETE")
     * @param Comment $comment
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Comment $comment, Request $request, ObjectManager $manager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->get('_token'))){
            $manager->remove($comment);
            $manager->flush();
            $this->addFlash('success', 'Votre suppression a été effectuée avec succès !');
        }

        return  $this->redirectToRoute('comment_admin');

    }
}
