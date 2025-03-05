<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Form\SeanceType;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface; // Ajout de l'importation
use Symfony\Component\Mime\Email; // Ajout de l'importation
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/seance')]
final class SeanceController extends AbstractController
{
  
    #[Route('/api/seances', name: 'api_seances', methods: ['GET'])]
    public function getSeances(SeanceRepository $seanceRepository): JsonResponse
    {
        $seances = $seanceRepository->findAll();

        $data = array_map(fn($seance) => [
            'id' => $seance->getId(),
            'title' => $seance->getObjectifs(),
            'start' => $seance->getDateSeance()->format('Y-m-d H:i:s'),
            'duration' => $seance->getDuree()->format('H:i:s'),
        ], $seances);

        return new JsonResponse($data);
    }

      
    #[Route('/calendrier', name: 'app_calendrier')]
    public function calendrier(): Response
    {
        return $this->render('front/calendrier.html.twig');
    }

    #[Route('/test-email', name: 'app_test_email', methods: ['GET'])] // Ajout de la méthode GET
    public function testEmail(MailerInterface $mailer): Response {
        $email = (new Email())
            ->from('noreply@monapp.com')
            ->to('farahboukesra4@gmail.com')
            ->subject('🔔 Test Symfony Mailer')
            ->html('<p>Ceci est un test d\'envoi d\'e-mail avec Symfony Mailer !</p>');

        $mailer->send($email);

        return new Response('✅ E-mail envoyé ! Vérifie Mailtrap.');
    }
    #[Route('/', name: 'app_seance_index', methods: ['GET'])]
    public function index(
        SeanceRepository $seanceRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // Créer la requête de base
        $query = $seanceRepository->createQueryBuilder('s')
            ->getQuery();
    
        // Paginer les résultats
        $seances = $paginator->paginate(
            $query, // La requête
            $request->query->getInt('page', 1), // Numéro de page
            3 // Nombre d'éléments par page
        );
    
        return $this->render('seance/index.html.twig', [
            'seances' => $seances // Passer l'objet paginé
        ]);
    }

    #[Route('/new', name: 'app_seance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seance = new Seance();
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($seance);
                $entityManager->flush();

                return $this->redirectToRoute('app_seance_index');
            } else {
                dump($form->getErrors(true, false));
            }
        }

        return $this->render('seance/new.html.twig', [
            'form'   => $form->createView(),
            'seance' => $seance,
        ]);
    }

    #[Route('/{id}', name: 'app_seance_show', methods: ['GET'])]
    public function show(Seance $seance): Response
    {
        return $this->render('seance/show.html.twig', [
            'seance' => $seance,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seance $seance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_seance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seance/edit.html.twig', [
            'seance' => $seance,
            'form'   => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_seance_delete', methods: ['POST'])]
    public function delete(Request $request, Seance $seance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seance->getId(), $request->request->get('_token'))) {
            $entityManager->remove($seance);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_seance_index', [], Response::HTTP_SEE_OTHER);
    }

   
    // Dans SeanceController.php



    

}
