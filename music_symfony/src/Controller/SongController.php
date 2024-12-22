<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Playlist;
use App\Entity\Song;
use App\Form\CommentType;
use App\Form\PlaylistType;
use App\Form\SongType;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/song')]
final class SongController extends AbstractController
{
    #[Route(name: 'app_song_index', methods: ['GET'])]
    public function index(SongRepository $songRepository): Response
    {
        return $this->render('song/index.html.twig', [
            'songs' => $songRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_song_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($song);
            $entityManager->flush();

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('song/new.html.twig', [
            'song' => $song,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_song_show', methods: ['GET'])]
    public function show(Song $song): Response
    {
        return $this->render('song/show.html.twig', [
            'song' => $song,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_song_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Song $song, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('song/edit.html.twig', [
            'song' => $song,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_song_delete', methods: ['POST'])]
    public function delete(Request $request, Song $song, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $song->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($song);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_song_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/song/{songId}', name: 'song_detail')]
    public function showSong(int $songId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $song = $entityManager->getRepository(Song::class)->find($songId);
        if (!$song) {
            throw $this->createNotFoundException('La chanson demandée n\'existe pas.');
        }

        $comment = new Comment();
        $comment->setSong($song);
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été ajouté avec succès.');

            return $this->redirectToRoute('song_detail', ['songId' => $song->getId()]);
        }

        $comments = $song->getComments();

        return $this->render('song/detail.html.twig', [
            'song' => $song,
            'form' => $form->createView(),
            'comments' => $comments,
        ]);
    }

    #[Route('/song/{songId}/add-to-playlist', name: 'add_to_playlist')]
    public function addToPlaylist(int $songId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $song = $entityManager->getRepository(Song::class)->find($songId);

        if (!$song) {
            throw $this->createNotFoundException('La chanson demandée n\'existe pas.');
        }

        $playlists = $entityManager->getRepository(Playlist::class)->findBy(['user' => $this->getUser()]);

        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist, [
            'playlists' => $playlists,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('existing_playlist')->getData()) {
                    $playlist = $entityManager->getRepository(Playlist::class)->find($form->get('existing_playlist')->getData());
                } else {
                    $playlist->setUserID($this->getUser());
                    $entityManager->persist($playlist);
                    $entityManager->flush();
                }

                $playlist->getSongs()->add($song);
                $entityManager->flush();

                $this->addFlash('success', 'La chanson a été ajoutée à la playlist.');

                return $this->redirectToRoute('playlist_detail', ['playlistId' => $playlist->getId()]);
            }

            return $this->render('song/add_to_playlist.html.twig', [
                'form' => $form->createView(),
                'song' => $song,
            ]);
        }
    }
}
