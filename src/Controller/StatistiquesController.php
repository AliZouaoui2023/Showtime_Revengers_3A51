<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Model\Chart;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques', name: 'statistiques_produits')]
    public function statistiquesProduits(ProduitRepository $produitRepository): Response
    {
        // Récupérer tous les produits
        $produits = $produitRepository->findAll();

        // Compter le nombre de produits par catégorie
        $categories = [];
        foreach ($produits as $produit) {
            $categorie = $produit->getCategorie();
            if (!isset($categories[$categorie])) {
                $categories[$categorie] = 0;
            }
            $categories[$categorie]++;
        }

        // Préparer les données pour le graphique
        $data = [
            'labels' => array_keys($categories),
            'datasets' => [
                [
                    'label' => 'Nombre de produits par catégorie',
                    'data' => array_values($categories),
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                    'borderColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                    'borderWidth' => 1,
                ]
            ]
        ];

        // Création du graphique à barres
        $chart = new Chart(Chart::TYPE_BAR);
        $chart->setData($data);
        $chart->setOptions([
            'responsive' => true,
            'plugins' => [
                'legend' => ['display' => true],
            ],
            'scales' => [
                'x' => ['beginAtZero' => true],
                'y' => ['beginAtZero' => true],
            ],
        ]);

        // Retourner la vue avec le graphique
        return $this->render('produit/stat.html.twig', [
            'chart' => $chart,
        ]);
    }
}