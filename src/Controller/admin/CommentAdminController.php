<?php

namespace App\Controller\admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentAdminController
 * @package App\Controller\admin
 * @IsGranted("ROLE_ADMIN")
 */
class CommentAdminController extends AbstractController
{
    /**
     * @Route("/admin/comment", name="comment_admin")
     * @param CommentRepository $commentRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(CommentRepository $commentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $q = $request->query->get('q');

        $queryBuilder = $commentRepository->getWithSearchQueryBuilder($q);

        $comments = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10 /*limit per page*/
        );

        return $this->render('comment_admin/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("admin/comment/{id}", name="comment_admin_delete", methods= "DELETE")
     * @param Comment $comment
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->get('_token'))){
                $manager->remove($comment);
                $manager->flush();
                $this->addFlash('success', 'Votre suppression a été effectuée avec succès !');
        }

        return  $this->redirectToRoute('comment_admin');
    }
}
