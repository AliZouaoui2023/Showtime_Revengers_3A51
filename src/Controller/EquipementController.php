<?php

// src/Controller/EquipementController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Form\EquipementType;
use App\Entity\Equipement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\EquipementRepository;

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
        // Récupérer tous les équipements depuis la base de données
        $equipements = $this->entityManager->getRepository(Equipement::class)->findAll();

        // Afficher la vue Twig avec les équipements
        return $this->render('equipement/table_equipements/basic_elements.html.twig', [
            'equipements' => $equipements,
        ]);
    }

    #[Route('/index_backend', name: 'index')]
    public function index2(): Response
    {
        // Afficher la page d'accueil
        return $this->render('Back/base.html.twig');
    }

    #[Route('/equipements/ajouter', name: 'equipement_add')]
    public function add(Request $request): Response
    {
        // Créer un nouvel objet Equipement
        $equipement = new Equipement();

        // Créer le formulaire
        $form = $this->createForm(EquipementType::class, $equipement);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'équipement dans la base de données
            $this->entityManager->persist($equipement);
            $this->entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'L\'équipement a bien été ajouté.');

            // Rediriger vers la page des équipements
            return $this->redirectToRoute('table_des_equipements');
        }

        // Afficher le formulaire
        return $this->render('equipement/forms/Ajout_equipement.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/equipements/modifier/{id}', name: 'equipement_edit')]
    public function edit(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire de modification
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les modifications
            $entityManager->flush();

            // Ajouter un message flash
            $this->addFlash('success', 'Équipement modifié avec succès.');

            // Rediriger vers la page des équipements
            return $this->redirectToRoute('table_des_equipements');
        }

        // Afficher le formulaire de modification
        return $this->render('equipement/forms/modifier_equipement.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }
       

    #[Route('/equipements/supprimer/{id}', name: 'equipement_delete')]
    public function delete(int $id): Response
    {
        // Récupérer l'équipement à supprimer
        $equipement = $this->entityManager->getRepository(Equipement::class)->find($id);

        // Vérifier si l'équipement existe
        if (!$equipement) {
            throw $this->createNotFoundException('Équipement non trouvé');
        }

        // Supprimer l'équipement
        $this->entityManager->remove($equipement);
        $this->entityManager->flush();

        // Ajouter un message flash
        $this->addFlash('success', 'Équipement supprimé avec succès.');

        // Rediriger vers la page des équipements
        return $this->redirectToRoute('table_des_equipements');
    }
    

}