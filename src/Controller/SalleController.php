<?php

namespace App\Controller;
use App\Entity\Notification;
use App\Entity\Salle;

use App\Form\SalleType;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\NotificationRepository;
use Knp\Component\Pager\PaginatorInterface;
#[Route('/salle')]
final class SalleController extends AbstractController
{
    private $notificationRepository;

    // Injecter NotificationRepository dans le constructeur
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }
    #[Route('/salles', name: 'app_salle_indexx', methods: ['GET'])]
    public function index(SalleRepository $salleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $status = $request->query->get('status', ''); // Récupérer le statut filtré
       
        // Construire la requête avec ou sans filtre de statut
        $queryBuilder = $salleRepository->createQueryBuilder('s');
       
        if ($status) {
            $queryBuilder->andWhere('s.statut = :status')
                         ->setParameter('status', $status);
        }
       
        // Ajouter le tri par statut
        $queryBuilder->orderBy('s.statut', 'ASC'); // ou 'DESC' selon votre besoin
       
        // Appliquer la pagination
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Numéro de page (par défaut 1)
            5 // Nombre de salles par page
        );
       
        return $this->render('salle/index.html.twig', [
            'salles' => $pagination,
            'status' => $status, // Passer le statut sélectionné
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
           
            // Création et enregistrement de la notification
            $notification = new Notification("Une nouvelle salle '{$salle->getNomSalle()}' a été ajoutée.");
            $entityManager->persist($notification);
           
            $entityManager->flush();
   
            return $this->redirectToRoute('app_salle_index', [], Response::HTTP_SEE_OTHER);
        }
   
        $notifications = $this->notificationRepository->findAllOrderedByDate(); // Récupérer les notifications
   
        return $this->render('salle/Ajouter_salle.html.twig', [
            'salle' => $salle,
            'form' => $form,
            'notifications' => $notifications, // Passer les notifications au template
        ]);
    }
   

 





    #[Route('/{id}/edit', name: 'app_salle_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Salle $salle, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'ancien statut avant la modification
    $ancienStatut = $salle->getStatut();

    $form = $this->createForm(SalleType::class, $salle);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer le nouveau statut après la modification
        $nouveauStatut = $salle->getStatut();

        // Vérifier si le statut a été modifié en "maintenance" ou "fermée"
        if (in_array($nouveauStatut, ['maintenance', 'fermee']) && $nouveauStatut !== $ancienStatut) {
            // Créer et enregistrer la notification
            $notification = new Notification("La salle '{$salle->getNomSalle()}' a été modifiée. Nouveau statut : {$nouveauStatut}.");
            $entityManager->persist($notification);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_salle_indexx', [], Response::HTTP_SEE_OTHER);
    }

    // Récupérer les notifications pour les afficher dans le template
    $notifications = $this->notificationRepository->findAllOrderedByDate();

    return $this->render('salle/modifier_salle.html.twig', [
        'salle' => $salle,
        'form' => $form,
        'notifications' => $notifications, // Passer les notifications au template
    ]);
}


#[Route('/salles/recherche', name: 'salle_recherche', methods: ['GET'])]
public function recherche(Request $request, SalleRepository $salleRepository): Response
{
    $searchTerm = $request->query->get('searchTerm');
    $salles = [];  // Initialisation de la variable

    // Recherche par ID
    if (is_numeric($searchTerm) && (int)$searchTerm > 0) {
        $salle = $salleRepository->find((int)$searchTerm);
        if ($salle) {
            $salles = [$salle];  // Tableau contenant une seule salle trouvée
        } else {
            // Si aucun résultat, laisse $salles vide
            $salles = [];
        }
    } else {
        // Si aucun terme de recherche valide, renvoie un tableau vide
        $salles = [];
    }

    // Debug pour vérifier le contenu de $salles
    dump($salles); // Ou dd($salles); si tu veux arrêter l'exécution

    return $this->render('salle/recherche_salles.html.twig', [
        'salles' => $salles,  // Passer les salles trouvées à la vue
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

       
        return $this->redirectToRoute('app_salle_indexx');
    }
}