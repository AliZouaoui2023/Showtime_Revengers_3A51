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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
class EquipementController extends AbstractController
{
    private $entityManager;

    // Injection du service EntityManagerInterface via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/basic_elements', name: 'table_des_equipements')]
 
public function tableDesEquipements(PaginatorInterface $paginator, Request $request): Response
{
    // Récupérer les paramètres de recherche
    $searchTerm = $request->query->get('searchTerm', '');
    $etat = $request->query->get('etat', ''); // Valeur du filtre d'état
    $order = $request->query->get('order', 'ASC'); // Récupérer l'ordre de tri (par défaut ASC)
   
    // Utiliser le repository pour construire la requête
    $queryBuilder = $this->entityManager->getRepository(Equipement::class)->createQueryBuilder('e');
   
    // Si un terme de recherche est spécifié
    if (!empty($searchTerm)) {
        $queryBuilder->andWhere('e.id LIKE :searchTerm') // Recherche par ID (ajuste si besoin)
                     ->setParameter('searchTerm', '%' . $searchTerm . '%');
    }
   
    // Si un filtre d'état est sélectionné
    if (!empty($etat)) {
        $queryBuilder->andWhere('e.etat = :etat')
                     ->setParameter('etat', $etat);
    }
   
    // Ajouter un tri sur l'état (en fonction du paramètre 'order')
    $queryBuilder->orderBy('e.etat', $order); // Tri dynamique selon l'état et l'ordre (ASC ou DESC)
   
    // Appliquer la pagination
    $equipements = $paginator->paginate(
        $queryBuilder,
        $request->query->getInt('page', 1), // Numéro de page (par défaut 1)
        5 // Nombre d'éléments par page
    );
   
    return $this->render('equipement/table_equipements/basic_elements.html.twig', [
        'equipements' => $equipements,
        'etat' => $etat, // Passer l'état pour le pré-sélectionner dans le formulaire
        'searchTerm' => $searchTerm, // Passer le terme de recherche pour le pré-remplir dans le formulaire
        'order' => $order, // Passer l'ordre de tri pour la pré-sélection dans le formulaire
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

   
    #[Route('/equipements/recherche', name: 'equipement_recherche', methods: ['GET'])]
    public function recherche(Request $request, EquipementRepository $equipementRepository): Response
    {
        $searchTerm = $request->query->get('searchTerm');
   
        // Vérifiez si searchTerm est un entier et s'il est valide
        if (is_numeric($searchTerm) && (int)$searchTerm > 0) {
            // Recherche par ID
            $equipement = $equipementRepository->find((int)$searchTerm);
   
            // Vérifier si l'équipement a été trouvé
            if ($equipement) {
                $equipements = [$equipement];  // Un tableau avec un seul équipement
            } else {
                $equipements = [];  // Aucun équipement trouvé
            }
        } else {
            $equipements = [];  // Aucun terme de recherche valide
        }
   
        return $this->render('equipement/forms/details_equipements.html.twig', [
            'equipements' => $equipements,
        ]);
    }
   

     

   
    #[Route('/ssstatistiques', name: 'statistiques_equipementss')]
    public function statistiques(EquipementRepository $equipementRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les statistiques d'équipements
$equipementsEnService = $equipementRepository->count(['etat' => 'En service']);
$equipementsHorsService = $equipementRepository->count(['etat' => 'Hors service']);
$equipementsEndommage = $equipementRepository->count(['etat' => 'Endommagé']);

// Récupérer les équipements pour la répartition par type
$repo = $entityManager->getRepository(Equipement::class);
$equipements = $repo->findAll();
$equipementsParType = [];

foreach ($equipements as $equipement) {
    $type = $equipement->getType();
    if (!isset($equipementsParType[$type])) {
        $equipementsParType[$type] = 0;
    }
    $equipementsParType[$type]++;
}

// Retourner les données à la vue
return $this->render('equipement/Statistique.html.twig', [
    'equipements_en_service' => $equipementsEnService,
    'equipements_hors_service' => $equipementsHorsService,
    'equipements_endommage' => $equipementsEndommage,
    'equipements_par_type_labels' => array_keys($equipementsParType), // Types des équipements
    'equipements_par_type_values' => array_values($equipementsParType), // Quantité d'équipements par type
]);
    }
}