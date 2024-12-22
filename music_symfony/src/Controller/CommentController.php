<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Song;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment')]
final class CommentController extends AbstractController
{
    #[Route(name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/song/{songId}/comment/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $songId): Response
    {
        $song = $entityManager->getRepository(Song::class)->find($songId);
        if (!$song) {
            throw $this->createNotFoundException('La chanson demandée n\'existe pas.');
        }
        $comment = new Comment();
        $comment->setSong($song);

        $now = new \DateTime();
        $comment->setCreatedAt($now);
        $user = $this->getUser();

        if ($user) {
            $comment->setCommentor($user);
        }
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('song_detail', ['songId' => $song->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/new.html.twig', [
            'form' => $form,
            'song' => $song,
        ]);
    }

    #[Route('/song/{songId}/comments', name: 'app_comment_show', methods: ['GET'])]
    public function show(int $songId, EntityManagerInterface $entityManager): Response
    {
        $song = $entityManager->getRepository(Song::class)->find($songId);
        if (!$song) {
            throw $this->createNotFoundException('La chanson demandée n\'existe pas.');
        }
        $comments = $song->getComments();
        return $this->render('comment/show.html.twig', [
            'song' => $song,
            'comments' => $comments,
        ]);
    }

    #[Route('/comment/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {

        if ($comment->getCommentor()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce commentaire.');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {

        if ($comment->getCommentor()->getId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce commentaire.');
        }
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
    }
}
