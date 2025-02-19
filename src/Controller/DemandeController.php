<?php



namespace App\Controller;

use App\Entity\Demande;
use App\Form\DemandeType;

use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/demande')]
final class DemandeController extends AbstractController
{
    #[Route(name: 'app_demande_index', methods: ['GET'])]
    public function index(DemandeRepository $demandeRepository): Response
    {
        return $this->render('demande/index.html.twig', [
            'demandes' => $demandeRepository->findAll(),
        ]);
    }
    #[Route('/dd', name: 'app_demande_indexx', methods: ['GET'])]
    public function indexx(DemandeRepository $demandeRepository): Response
    {
        return $this->render('demande/indexdemande.html.twig', [
            'demandes' => $demandeRepository->findAll(),
        ]);
    }


    

    #[Route('/new', name: 'app_demande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demande);
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande/new.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }
  


    #[Route('/home', name: 'app_front_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('Front/base.html.twig');
    }

    #[Route('/newdemande', name: 'app_demande_newdemande', methods: ['GET', 'POST'])]
    public function newdemande(Request $request, EntityManagerInterface $entityManager): Response
    {
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demande);
            $entityManager->flush();

            return $this->redirectToRoute('app_front_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande/newdemande.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_show', methods: ['GET'])]
    public function show(Demande $demande): Response
    {
        return $this->render('demande/show.html.twig', [
            'demande' => $demande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Demande $demande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande/edit.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editt', name: 'app_demande_editt', methods: ['GET', 'POST'])]
    public function editt(Request $request, Demande $demande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande/edit.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/edittt', name: 'app_demande_edittt', methods: ['GET', 'POST'])]
    public function edittt(Request $request, int $id, DemandeRepository $demandeRepository, EntityManagerInterface $entityManager): Response
    {
        $demande = $demandeRepository->find($id);
    
        if (!$demande) {
            throw $this->createNotFoundException('Demande not found');
        }
    
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('demande/editdemande.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }
    
    


    #[Route('/{id}', name: 'app_demande_delete', methods: ['POST'])]
    public function delete(Request $request, Demande $demande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $demande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($demande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/demande/{id}/valider', name: 'app_demande_validate', methods: ['POST'])]
    public function validate(Request $request, Demande $demande, EntityManagerInterface $entityManager): Response
    {
      
        if (!$this->isCsrfTokenValid('validate' . $demande->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

    
        $demande->setStatut('approuvee');
        $entityManager->flush();

  
        $this->addFlash('success', 'Demande validée avec succès');
        return $this->redirectToRoute('app_demande_index');
    }
    
}

