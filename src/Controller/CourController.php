<?php

namespace App\Controller;

use App\Entity\Cour;
use App\Entity\Client;
use App\Form\CourType;
use App\Repository\CourRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cour')]
final class CourController extends AbstractController{
    #[Route(name: 'app_cour_index', methods: ['GET'])]
    public function index(CourRepository $courRepository): Response
    {
        return $this->render('cour/index.html.twig', [
            'cours' => $courRepository->findAll(),
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
    public function show(Cour $cour): Response
    {
        return $this->render('cour/show.html.twig', [
            'cour' => $cour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cour_edit', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('app_cour_index');
        }

        return $this->render('cour/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_cour_delete', methods:['GET', 'POST'])]
public function delete(
    Request $request, 
    Cour $cour, 
    EntityManagerInterface $entityManager
): Response {
    if ($this->isCsrfTokenValid('delete' . $cour->getId(), $request->request->get('_token'))) {
        $entityManager->remove($cour);
        $entityManager->flush();
        $this->addFlash('success', 'Cours supprimé avec succès');
    } else {
        $this->addFlash('error', 'Erreur de sécurité');
    }

    return $this->redirectToRoute('app_cour_index');
}


    #[Route('/{id}/inscrire/{clientId}', name: 'app_cour_inscrire', methods: ['POST'])]
    public function inscrireClient(int $id, int $clientId, CourRepository $courRepository, ClientRepository $clientRepository, EntityManagerInterface $entityManager): Response
    {
        $cour = $courRepository->find($id);
        $client = $clientRepository->find($clientId);

        if (!$cour || !$client) {
            return $this->redirectToRoute('app_cour_index');
        }

        if (!$client->getCours()->contains($cour)) {
            $client->addCour($cour);
            $entityManager->persist($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cour_show', ['id' => $id]);
    }
}
