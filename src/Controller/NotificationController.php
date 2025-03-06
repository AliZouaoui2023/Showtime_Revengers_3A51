<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification;

class NotificationController extends AbstractController
{
    private $entityManager;

    // Injection de EntityManagerInterface via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Récupère toutes les notifications triées par date de création décroissante.
     *
     * @return Notification[]
     */
    public function getNotifications(): array
    {
        return $this->entityManager->getRepository(Notification::class)->findBy(
            [], // Pas de filtre spécifique
            ['createdAt' => 'DESC'] // Tri par date de création, décroissant
        );
    }












   
   
    #[Route("/notifications", name: "notifications_list")]
    public function listNotifications(): Response
    {
        // Supprimer les notifications expirées
        $this->deleteOldNotifications();
   
        // Récupérer les notifications récentes
        $notifications = $this->getNotifications();
        $notificationCount = count($notifications);
   
        return $this->render('salle/notification.html.twig', [
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
        ]);
    }



   
   
 
public function deleteOldNotifications(): void
{
    $twoDaysAgo = new \DateTime('-5 minutes');//-5 minutes

    $this->entityManager->createQueryBuilder()
        ->delete(Notification::class, 'n')
        ->where('n.createdAt < :dateLimit')
        ->setParameter('dateLimit', $twoDaysAgo)
        ->getQuery()
        ->execute();
}
// Exemple de contrôleur
// Exemple de contrôleur
// Assure-toi de définir la variable notificationCount dans ton contrôleur






   

}
