<?php

// src/Controller/EquipementController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Form\EquipementType;
use App\Entity\Equipement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\EquipementRepository;
use App\Repository\SalleRepository;

use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class EquipementController extends AbstractController
{
    private $entityManager;

    // Injection du service EntityManagerInterface via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/basic_elements', name: 'table_des_equipements')]
    public function tableDesEquipements(): Response
    {
 
        $equipements = $this->entityManager->getRepository(Equipement::class)->findAll();

       
        return $this->render('equipement/table_equipements/basic_elements.html.twig', [
            'equipements' => $equipements,
        ]);
    }

    #[Route('/index_backend', name: 'index')]
    public function index2(): Response
    {
      
        return $this->render('Back/base.html.twig');
    }

    #[Route('/equipements/ajouter', name: 'equipement_add')]
    public function add(Request $request): Response
    {
      
        $equipement = new Equipement();

        $form = $this->createForm(EquipementType::class, $equipement);

       
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $this->entityManager->persist($equipement);
            $this->entityManager->flush();

            
            $this->addFlash('success', 'L\'équipement a bien été ajouté.');

            
            return $this->redirectToRoute('table_des_equipements');
        }

        
        return $this->render('equipement/forms/Ajout_equipement.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/equipements/modifier/{id}', name: 'equipement_edit')]
    public function edit(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
      
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();
 
            $this->addFlash('success', 'Équipement modifié avec succès.');

           
            return $this->redirectToRoute('table_des_equipements');
        }

        
        return $this->render('equipement/forms/modifier_equipement.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }
       

    #[Route('/equipements/supprimer/{id}', name: 'equipement_delete')]
    public function delete(int $id): Response
    {
   
        $equipement = $this->entityManager->getRepository(Equipement::class)->find($id);

       
        if (!$equipement) {
            throw $this->createNotFoundException('Équipement non trouvé');
        }

       
        $this->entityManager->remove($equipement);
        $this->entityManager->flush();

 
        $this->addFlash('success', 'Équipement supprimé avec succès.');

        
        return $this->redirectToRoute('table_des_equipements');
    }

    

    #[Route('/statistiques', name: 'statistiques_equipements')]
public function index(EquipementRepository $equipementRepository): Response
{
    // Récupération des statistiques des équipements
    $equipementsEnService = $equipementRepository->count(['etat' => 'En service']);
    $equipementsHorsService = $equipementRepository->count(['etat' => 'Hors service']);
    $equipementsEndommage = $equipementRepository->count(['etat' => 'Endommagé']);

    return $this->render('equipement/statistique.html.twig', [
        'equipements_en_service' => $equipementsEnService,
        'equipements_hors_service' => $equipementsHorsService,
        'equipements_endommage' => $equipementsEndommage,
    ]);
}

}










    

