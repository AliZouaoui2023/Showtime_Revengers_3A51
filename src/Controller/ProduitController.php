<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route(name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $produit->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_produit_new');
                }
            } else {
                $produit->setImage('default_image.jpg'); // Image par défaut
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $imagePath = $this->getParameter('images_directory') . '/' . $produit->getImage();

            if ($produit->getImage() && file_exists($imagePath) && $produit->getImage() !== 'default_image.jpg') {
                unlink($imagePath);
            }

            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produit_index');
    }

    #[Route('/produit/search', name: 'app_produit_search', methods: ['GET'])]
    public function search(Request $request, ProduitRepository $produitRepository): Response
    {
        // Récupération des paramètres de recherche
        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', 'nom');
        $direction = $request->query->get('direction', 'ASC');
    
        // Recherche et tri des produits
        $produits = $produitRepository->searchAndSortProducts($search, $sort, $direction);
    
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
    
}