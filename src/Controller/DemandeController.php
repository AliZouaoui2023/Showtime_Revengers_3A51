<?php



namespace App\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Demande;
use App\Form\DemandeType;
use App\Entity\User;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/demande')]
final class DemandeController extends AbstractController
{
    #[Route(name: 'app_demande_index', methods: ['GET'])]
    public function index(DemandeRepository $demandeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // Récupérer toutes les demandes sous forme de query (non exécutée)
        $query = $demandeRepository->createQueryBuilder('d')
            ->getQuery();
    
        // Paginer les résultats avec KnpPaginator
        $pagination = $paginator->paginate(
            $query, // La requête à paginer
            $request->query->getInt('page', 1), // Numéro de page, par défaut 1
            10 // Nombre d'éléments par page (ajustez selon vos besoins)
        );
    
        return $this->render('demande/index.html.twig', [
            'demandes' => $pagination,
        ]);
    }
    #[Route('/dd', name: 'app_demande_indexx', methods: ['GET'])]
    public function indexx(DemandeRepository $demandeRepository): Response
    {
        return $this->render('demande/indexdemande.html.twig', [
            'demandes' => $demandeRepository->findAll(),
        ]);
    }
    #[Route('/ddd', name: 'app_demande_ind', methods: ['GET'])]
    public function ind(DemandeRepository $demandeRepository, RequestStack $requestStack): Response
    {
        // Récupérer l'utilisateur connecté depuis la session
        $session = $requestStack->getSession();
        $userId = $session->get('user'); // Assurez-vous que 'user' stocke bien l'ID de l'utilisateur
    
        if (!$userId) {
            // Si aucun utilisateur n'est connecté, vous pouvez rediriger ou afficher un message d'erreur
            return $this->redirectToRoute('frontend_login');  // Par exemple, rediriger vers la page de connexion
        }
    
        // Récupérer les demandes pour cet utilisateur
        $demandes = $demandeRepository->findBy(['user' => $userId]);
    
        // Renvoyer les demandes uniquement pour l'utilisateur connecté
        return $this->render('demande/indexdemande.html.twig', [
            'demandes' => $demandes,
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
public function newdemande(Request $request, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
{
    // Récupérer l'utilisateur connecté depuis la session
    $session = $requestStack->getSession();
    $userId = $session->get('user'); // Assurez-vous que 'user' stocke bien l'ID de l'utilisateur

    if (!$userId) {
        // Si aucun utilisateur n'est connecté, rediriger vers la page de connexion
        return $this->redirectToRoute('frontend_login');
    }

    // Récupérer l'utilisateur depuis la base de données
    $user = $entityManager->getRepository(User::class)->find($userId);

    if (!$user) {
        // Si l'utilisateur n'existe pas, rediriger vers la connexion
        return $this->redirectToRoute('frontend_login');
    }

    // Créer une nouvelle demande et associer uniquement l'utilisateur
    $demande = new Demande();
    $demande->setUser($user); // Associer l'utilisateur connecté

    // Créer le formulaire (on ne touche pas aux champs user et admin dans Twig)
    $form = $this->createForm(DemandeType::class, $demande);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($demande);
        $entityManager->flush();

        return $this->redirectToRoute('app_demande_indexx', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('demande/newdemande.html.twig', [
        'demande' => $demande,
        'form' => $form->createView(),
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
    
        return new Response('', Response::HTTP_NO_CONTENT);
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
    #[Route('/demande/pdf/{id}', name: 'app_demande_pdf', methods: ['GET'])]
    public function generatePdf(Demande $demande): Response
    {
        // Configurer Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);

        // Rendre le contenu HTML pour le PDF avec Twig
        $html = $this->renderView('demande/pdf_template.html.twig', [
            'demande' => $demande,
        ]);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Configurer le format du papier (A4 par défaut)
        $dompdf->setPaper('A4', 'portrait');

        // Générer le PDF
        $dompdf->render();

        // Nom du fichier PDF
        $fileName = 'demande_' . $demande->getId() . '.pdf';

        // Retourner le PDF en téléchargement
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    
}

