<?php

namespace App\Controller;

use App\Entity\Publicite;
use App\Form\PubliciteType;
use App\Repository\PubliciteRepository;
use App\Repository\DemandeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/publicite')]
class PubliciteController extends AbstractController
{
    #[Route('/', name: 'app_publicite_index', methods: ['GET'])]
    public function index(PubliciteRepository $publiciteRepository): Response
    {
        // Récupère toutes les publicités
        $publicites = $publiciteRepository->findAll();

        // Rendre la vue avec les publicités
        return $this->render('publicite/index.html.twig', [
            'publicites' => $publicites,
        ]);
    }


    #[Route('/new', name: 'app_publicite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, DemandeRepository $demandeRepository): Response
    {
        $publicite = new Publicite();

        // Vérifie si une demande a été validée et récupère ses données
        $demandeId = $request->query->get('demandeId');
        if ($demandeId) {
            $demande = $demandeRepository->find($demandeId);
            if ($demande) {
                // Récupérer les données de la demande et les transférer à la publicité
                $publicite->setDemande($demande);

                // Calculer le montant automatiquement à partir de la demande
                $montant = $demande->calculerMontant();
                $publicite->setMontant($montant);

                // Récupérer le support depuis le champ 'lienSupp' de la demande
                $publicite->setSupport($demande->getLienSupp());

                // Définir la date de début comme la date de soumission de la demande
                $dateDebut = $demande->getDateSoumission();
                $publicite->setDateDebut($dateDebut);

                // Définir la date de fin en fonction du nombre de jours
                $dateFin = (clone $dateDebut)->modify("+{$demande->getNbrJours()} days");
                $publicite->setDateFin($dateFin);
            }
        }

        // Créer le formulaire pour la publicité
        $form = $this->createForm(PubliciteType::class, $publicite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste et sauvegarde la publicité
            $entityManager->persist($publicite);
            $entityManager->flush();

            // Redirige vers la page d'index des publicités
            return $this->redirectToRoute('app_publicite_index');
        }

        return $this->render('publicite/new.html.twig', [
            'publicite' => $publicite,
            'form' => $form,
        ]);
    }
   


    #[Route('/{id}', name: 'app_publicite_show', methods: ['GET'])]
    public function show(Publicite $publicite): Response
    {
        return $this->render('publicite/show.html.twig', [
            'publicite' => $publicite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_publicite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Publicite $publicite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PubliciteType::class, $publicite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_publicite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publicite/edit.html.twig', [
            'publicite' => $publicite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_publicite_delete', methods: ['POST'])]
    public function delete(Request $request, Publicite $publicite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publicite->getId(), $request->get('_token'))) {
            $entityManager->remove($publicite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_publicite_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/publicites/events', name: 'app_publicite_events', methods: ['GET'])]
    public function getPublicitesEvents(PubliciteRepository $publiciteRepository): JsonResponse
    {
        $publicites = $publiciteRepository->findAll();
        $events = [];
    
        foreach ($publicites as $publicite) {
            // Get the start and end dates
            $startDate = $publicite->getDateDebut();
            $endDate = $publicite->getDateFin();
    
            // Add one day to the end date for FullCalendar (since end date is exclusive)
            if ($endDate) {
                $endDate = (clone $endDate)->modify('+1 day');
            }
    
            $events[] = [
                'title' => 'Publicité #' . $publicite->getId(),
                'start' => $startDate ? $startDate->format('Y-m-d') : null,
                'end' => $endDate ? $endDate->format('Y-m-d') : null,
                'url' => $this->generateUrl('app_publicite_show', ['id' => $publicite->getId()]),
            ];
        }
    
        return new JsonResponse($events);
    }
    
}
