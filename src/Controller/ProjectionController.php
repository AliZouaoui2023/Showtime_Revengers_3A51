<?php

namespace App\Controller;

use App\Entity\Projection;
use App\Form\ProjectionType;
use App\Repository\ProjectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projection')]
final class ProjectionController extends AbstractController
{
    #[Route(name: 'app_projection_index', methods: ['GET'])]
    public function index(ProjectionRepository $projectionRepository): Response
    {
        return $this->render('projection/index.html.twig', [
            'projections' => $projectionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_projection_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projection = new Projection();
        $form = $this->createForm(ProjectionType::class, $projection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projection);
            $entityManager->flush();

            return $this->redirectToRoute('app_projection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projection/new.html.twig', [
            'projection' => $projection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_projection_show', methods: ['GET'])]
    public function show(Projection $projection): Response
    {
        return $this->render('projection/show.html.twig', [
            'projection' => $projection,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_projection_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projection $projection, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectionType::class, $projection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_projection_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projection/edit.html.twig', [
            'projection' => $projection,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_projection_delete', methods: ['POST'])]
    public function delete(Request $request, Projection $projection, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projection->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($projection);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projection_index', [], Response::HTTP_SEE_OTHER);
    }
}
