<?php

namespace App\Controller;

use App\Entity\Films;
use App\Form\FilmsType;
use App\Repository\FilmsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\FilmSearchType;


#[Route('/films')]
final class FilmsController extends AbstractController
{
    #[Route(name: 'app_films_index', methods: ['GET'])]
    public function index(FilmsRepository $filmsRepository): Response
    {
        return $this->render('films/index.html.twig', [
            'films' => $filmsRepository->findAll(),
        ]);
    }

    #[Route('/films/search', name: 'app_films_search', methods: ['GET'])]
    public function search(Request $request, FilmsRepository $filmsRepository): Response
    {
        $form = $this->createForm(FilmSearchType::class);
        $form->handleRequest($request);

        $query = $form->get('query')->getData();
        $films = $filmsRepository->searchFilms($query);

        return $this->render('films/search.html.twig', [
            'films' => $films,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/films/stats', name: 'app_films_stats')]
public function stats(FilmsRepository $filmsRepository): Response
{
    $stats = $filmsRepository->countFilmsByGenre();

    // Extract genres and counts separately
    $genres = [];
    $counts = [];

    foreach ($stats as $stat) {
        $genres[] = $stat['Genre'];
        $counts[] = $stat['count'];
    }

    return $this->render('films/stats.html.twig', [
        'genres' => json_encode($genres),
        'counts' => json_encode($counts),
    ]);
}


    #[Route('/filmList',name: 'app_films_List', methods: ['GET'])]  
    public function test(FilmsRepository $filmsRepository): Response
    {
        return $this->render('films/films.html.twig', [
            'films' => $filmsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_films_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $film = new Films();
        $form = $this->createForm(FilmsType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('app_films_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('films/new.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_films_show', methods: ['GET'])]
    public function show(Films $film): Response
    {
        return $this->render('films/show.html.twig', [
            'film' => $film,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_films_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Films $film, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FilmsType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_films_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('films/edit.html.twig', [
            'film' => $film,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_films_delete', methods: ['POST'])]
    public function delete(Request $request, Films $film, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$film->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($film);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_films_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
