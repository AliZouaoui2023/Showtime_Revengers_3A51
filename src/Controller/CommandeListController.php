<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit; // Assurez-vous que vous importez l'entité Produit
use App\Repository\ProduitRepository; // Importez correctement le repository Produit
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/commandes')]
final class CommandeListController extends AbstractController
{
    #[Route(name: 'app_commande_list', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/list.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }
}