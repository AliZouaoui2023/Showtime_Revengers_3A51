<?php

namespace App\Controller;

use App\Entity\Cour;
use App\Entity\User;
use App\Form\CourType;
use App\Repository\CourRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cour')]
final class CourController extends AbstractController
{
    #[Route(name: 'app_cour_index', methods: ['GET'])]
    public function index(CourRepository $courRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté (à adapter selon votre système d'authentification)
        $user = $entityManager->getRepository(User::class)->find(3); // Remplacez par l'utilisateur connecté

        // Récupérer tous les cours
        $cours = $courRepository->findAll();

        // Créer un tableau pour stocker l'état de l'inscription
        $isInscrit = [];
        foreach ($cours as $cour) {
            $isInscrit[$cour->getId()] = $user ? $cour->getUsers()->contains($user) : false;
        }

        return $this->render('cour/index.html.twig', [
            'cours' => $cours,
            'isInscrit' => $isInscrit, // Passer l'état de l'inscription au template
        ]);
    }

    #[Route('/newfront', name: 'app_cour_indexx', methods: ['GET'])]
    public function indexx(Request $request, CourRepository $courRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find(3); // Remplacez par l'utilisateur connecté
    
        $types = $courRepository->findDistinctTypes();
        $selectedType = $request->query->get('type');
    
        // Récupérer les cours triés
        if ($selectedType) {
            $cours = $courRepository->createQueryBuilder('c')
                ->where('c.typeCour = :type')
                ->setParameter('type', $selectedType)
                ->orderBy('c.dateDebut', 'ASC')
                ->addOrderBy('c.dateFin', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            $cours = $courRepository->findAllSorted();
        }
    
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
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cour_show', methods: ['GET'])]
    public function show(Cour $cour, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté (à adapter selon votre système d'authentification)
        $user = $entityManager->getRepository(User::class)->find(3); // Remplacez par l'utilisateur connecté

        // Vérifier si l'utilisateur est inscrit
        $isInscrit = $user ? $cour->getUsers()->contains($user) : false;

        return $this->render('cour/show.html.twig', [
            'cour' => $cour,
            'isInscrit' => $isInscrit, // Passer l'état de l'inscription au template
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cour $cour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cour_index');
        }

        return $this->render('cour/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_cour_delete', methods: ['POST'])]
    public function delete(Request $request, Cour $cour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cour_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/inscrire', name: 'app_cour_inscrire', methods: ['POST'])]
    public function inscrire(Cour $cour, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté (à adapter selon votre système d'authentification)
        $user = $entityManager->getRepository(User::class)->find(3); // Remplacez par l'utilisateur connecté

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si l'utilisateur est déjà inscrit
        if ($cour->getUsers()->contains($user)) {
            // Désinscrire l'utilisateur
            $cour->removeUser($user);
            $message = 'Vous avez quitté ce cours.';
        } else {
            // Inscrire l'utilisateur
            $cour->addUser($user);
            $message = 'Participation enregistrée avec succès.';
        }

        $entityManager->persist($cour);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => $message,
            'isInscrit' => $cour->getUsers()->contains($user), // Retourne l'état actuel de l'inscription
        ]);
    }

    #[Route('/{id}/check-inscription', name: 'app_cour_check_inscription', methods: ['GET'])]
    public function checkInscription(Cour $cour, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté (à adapter selon votre système d'authentification)
        $user = $entityManager->getRepository(User::class)->find(3); // Remplacez par l'utilisateur connecté

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si l'utilisateur est déjà inscrit
        $isInscrit = $cour->getUsers()->contains($user);

        return $this->json([
            'success' => true,
            'isInscrit' => $isInscrit,
        ]);
    }

    #[Route('/{id}/details', name: 'app_cour_details', methods: ['GET'])]
public function details(Cour $cour, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'utilisateur connecté (à adapter selon votre système d'authentification)
    $user = $entityManager->getRepository(User::class)->find(3); // Remplacez par l'utilisateur connecté

    // Vérifier si l'utilisateur est inscrit
    $isInscrit = $user ? $cour->getUsers()->contains($user) : false;

    return $this->render('Front/details.html.twig', [
        'cour' => $cour,
        'isInscrit' => $isInscrit, // Passer l'état de l'inscription au template
    ]);
}}