<?php

namespace App\Controller;

use App\Entity\Cour;
use App\Entity\User;
use App\Form\CourType;
use App\Repository\CourRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\MailerService;

#[Route('/cour')]
final class CourController extends AbstractController
{
    private CourRepository $courRepository;

    public function __construct(CourRepository $courRepository)
    {
        $this->courRepository = $courRepository;
    }

    #[Route(name: 'app_cour_index', methods: ['GET'])]
    public function index(
        EntityManagerInterface $entityManager,
        RequestStack $requestStack,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $session = $requestStack->getSession();
        $userId = $session->get('user');
        $user = $entityManager->getRepository(User::class)->find($userId);

        $query = $this->courRepository->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->getQuery();

        $cours = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            3
        );

        $isInscrit = [];
        foreach ($cours as $cour) {
            $isInscrit[$cour->getId()] = $user ? $cour->getUsers()->contains($user) : false;
        }

        return $this->render('cour/index.html.twig', [
            'cours' => $cours,
            'isInscrit' => $isInscrit,
        ]);
    }

    #[Route('/newfront', name: 'app_cour_indexx', methods: ['GET'])]
    public function indexx(
        Request $request,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ): Response {
        $session = $request->getSession();
        $userId = $session->get('user');
        $user = $entityManager->getRepository(User::class)->find($userId);

        $types = $this->courRepository->findDistinctTypes();
        $selectedType = $request->query->get('type');

        $queryBuilder = $this->courRepository->createQueryBuilder('c');

        if ($selectedType) {
            $queryBuilder
                ->where('c.typeCour = :type')
                ->setParameter('type', $selectedType);
        }

        $queryBuilder
            ->orderBy('c.dateDebut', 'ASC')
            ->addOrderBy('c.dateFin', 'ASC');

        $cours = $queryBuilder->getQuery()->getResult();

        $isInscrit = [];
        foreach ($cours as $cour) {
            $isInscrit[$cour->getId()] = $user ? $cour->getUsers()->contains($user) : false;
        }

        return $this->render('Front/indexmabase.html.twig', [
            'cours' => $cours,
            'types' => $types,
            'selectedType' => $selectedType,
            'isInscrit' => $isInscrit,
        ]);
    }

    #[Route('/new', name: 'app_cour_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cour = new Cour();
        $form = $this->createForm(CourType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cour);
            $entityManager->flush();

            return $this->redirectToRoute('app_cour_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cour/new.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/statistiques', name: 'api_statistiques', methods: ['GET'])]
    public function getStatistiques(): JsonResponse
    {
        $statistiques = $this->courRepository->countCoursByType();

        return $this->json($statistiques);
    }

    #[Route('/cours/statistiques', name: 'app_cour_statistiques', methods: ['GET'])]
    public function statistiques(): Response
    {
        // Récupérer les statistiques par type_cour
        $statistiques = $this->courRepository->countCoursByType(); // Utiliser la méthode existante

        // Préparer les données pour le rendu
        $typesCour = [];
        $counts = [];

        foreach ($statistiques as $statistique) {
            $typesCour[] = $statistique['typeCour'];
            $counts[] = $statistique['count'];
        }

        return $this->render('cour/statistiques.html.twig', [
            'typesCour' => $typesCour,
            'counts' => $counts,
        ]);
    }

    #[Route('/{id}', name: 'app_cour_show', methods: ['GET'])]
    public function show(
        Cour $cour,
        EntityManagerInterface $entityManager,
        RequestStack $requestStack
    ): Response {
        $session = $requestStack->getSession();
        $userId = $session->get('user');
        $user = $entityManager->getRepository(User::class)->find($userId);
        $isInscrit = $user ? $cour->getUsers()->contains($user) : false;

        return $this->render('cour/show.html.twig', [
            'cour' => $cour,
            'isInscrit' => $isInscrit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cour_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Cour $cour,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(CourType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_cour_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cour/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_cour_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Cour $cour,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cour_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/inscrire', name: 'app_cour_inscrire', methods: ['POST'])]
    public function inscrire(
        Cour $cour,
        EntityManagerInterface $entityManager,
        RequestStack $requestStack
    ): JsonResponse {
        $session = $requestStack->getSession();
        $userId = $session->get('user');
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Utilisateur non connecté'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($cour->getUsers()->contains($user)) {
            $cour->removeUser($user);
            $message = 'Désinscription réussie';
        } else {
            $cour->addUser($user);
            $message = 'Inscription réussie';
        }

        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => $message,
            'isInscrit' => $cour->getUsers()->contains($user)
        ]);
    }

    #[Route('/{id}/check-inscription', name: 'app_cour_check_inscription', methods: ['GET'])]
    public function checkInscription(
        Cour $cour,
        EntityManagerInterface $entityManager,
        RequestStack $requestStack
    ): JsonResponse {
        $session = $requestStack->getSession();
        $userId = $session->get('user');
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'isInscrit' => $cour->getUsers()->contains($user)
        ]);
    }

    #[Route('/{id}/details', name: 'app_cour_details', methods: ['GET'])]
    public function details(Cour $cour): Response
    {
        return $this->render('Front/details.html.twig', [
            'cour' => $cour,
        ]);
    }
    }