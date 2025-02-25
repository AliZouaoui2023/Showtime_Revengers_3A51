<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produits')]
final class ProduitListController extends AbstractController
{
    #[Route(name: 'app_produit_list', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/list.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_produit_shows', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/single.html.twig', [
            'produit' => $produit, 
        ]);
    }
}