<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/salle')]
final class SalleController extends AbstractController
{
    #[Route(name: 'app_salle_index', methods: ['GET'])]
    

public function index(SalleRepository $salleRepository): Response
{
    $salles = $salleRepository->findAll();

    return $this->render('salle/index.html.twig', [
        'salles' => $salles,
    ]);
}


    #[Route('/new', name: 'app_salle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($salle);
            $entityManager->flush();

            return $this->redirectToRoute('app_salle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('salle/Ajouter_salle.html.twig', [
            'salle' => $salle,
            'form' => $form,
        ]);
    }

  

    #[Route('/{id}/edit', name: 'app_salle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Salle $salle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_salle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('salle/modifier_salle.html.twig', [
            'salle' => $salle,
            'form' => $form,
        ]);
    }

    #[Route('/salle/supprimer/{id}', name: 'app_salle_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
    
        $salle = $entityManager->getRepository(Salle::class)->find($id);

        
        if (!$salle) {
            throw $this->createNotFoundException('Salle non trouvée');
        }

 
        $entityManager->remove($salle);
        $entityManager->flush();

      
        $this->addFlash('success', 'Salle supprimée avec succès.');

       
        return $this->redirectToRoute('app_salle_index');
    }
   



}

