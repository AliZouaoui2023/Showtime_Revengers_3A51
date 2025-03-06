<?php
namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\MailerService;
use App\Service\SmsService;
use Twilio\Exceptions\TwilioException;


#[Route('/commande')]
final class CommandeController extends AbstractController
{
    #[Route(name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager, MailerService $mailerService): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            dump('Formulaire soumis et valide'); // Vérifie que le formulaire est bien validé
    
            if (strtolower($commande->getEtat()) === 'livrée') {
                dump('L\'email devrait être envoyé');
                dump('Email : ' . $commande->getUser()->getEmail());
                dump('Nom : ' . $commande->getUser()->getNom());
                dump('Commande ID : ' . $commande->getId());
    
                try {
                    $mailerService->sendOrderDeliveredEmail(
                        $commande->getUser()->getEmail(),
                        $commande->getUser()->getNom(),
                        $commande->getId()
                    );
                    dump('Email envoyé avec succès');
                } catch (\Exception $e) {
                    dump('Erreur d\'envoi d\'email : ' . $e->getMessage());
                }
            }
    
            $entityManager->flush();
            dump('Commande mise à jour');
    
            return $this->redirectToRoute('app_commande_index');
        }
    
        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/commander/{id}', name: 'commander', methods: ['GET', 'POST'])]
    public function commander(int $id, ProduitRepository $produitRepository, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        $produit = $produitRepository->find($id);
        if (!$produit) {
            $this->addFlash('error', 'Produit non trouvé!');
            return $this->redirectToRoute('app_produit_index');
        }

        $session = $requestStack->getSession();
        $userId = $session->get('user');

        if (!$userId) {
            $this->addFlash('error', 'Vous devez être connecté pour passer une commande!');
            return $this->redirectToRoute('frontend_login');
        }

        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé!');
            return $this->redirectToRoute('frontend_login');
        }

        $commande = new Commande();
        $commande->addProduit($produit);
        $commande->setMontantPaye($produit->getPrix());
        $commande->setUser($user);
        $commande->setEtat('en_attente');

        $entityManager->persist($commande);
        $entityManager->flush();

        $this->addFlash('success', 'Commande effectuée avec succès!');
        return $this->redirectToRoute('app_produit_list');
    }

    #[Route('/commander/{id}/pdf', name: 'commande_pdf', methods: ['GET', 'POST'])]
    public function generatePdf(int $id, EntityManagerInterface $em): Response
    {
        $commande = $em->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);

        $html = $this->renderView('commande/pdf.html.twig', [
            'commande' => $commande,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="commande_' . $commande->getId() . '.pdf"'
        ]);
    }

    #[Route('/commande/{id}/livree', name: 'commande_livree')]
    public function livree(int $id, EntityManagerInterface $entityManager, SmsService $smsService): Response
    {
        // Récupération de la commande
        $commande = $entityManager->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException("Commande avec l'ID $id non trouvée !");
        }

        // Vérification de l'état de la commande
        if (strtolower($commande->getEtat()) !== 'livrée') {
            $this->addFlash('error', 'La commande n\'est pas encore livrée.');
            return $this->redirectToRoute('app_commande_index');
        }

        // Récupérer l'utilisateur et le numéro de téléphone
        $user = $commande->getUser();
        $toPhoneNumber = $user ? $user->getTelephone() : null;

        if (!$toPhoneNumber) {
            $this->addFlash('error', 'Le numéro de téléphone de l\'utilisateur est manquant.');
            return $this->redirectToRoute('app_commande_index');
        }

        // Création du message SMS
        $message = "Bonjour {$user->getNom()}, votre commande #{$commande->getId()} a bien été livrée ! Merci pour votre confiance.";

        try {
            // Envoi du SMS en utilisant la méthode sendCommandeLivreeSms du service SmsService
            $smsService->sendCommandeLivreeSms($commande);
            $this->addFlash('success', 'SMS envoyé avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_commande_index');
    }
}