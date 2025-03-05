<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\Image;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        // Récupérer les paramètres de recherche depuis la requête
        $search = $request->query->get('search'); // Nom
        $email = $request->query->get('email');   // Email
        $role = $request->query->get('role');     // Rôle
    
        // Utiliser la méthode du repository pour filtrer les utilisateurs
        $users = $userRepository->findByCriteria($search, $email, $role);
    
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }
    
    #[Route('/stats', name: 'app_user_stats', methods: ['GET'])]
public function stats(UserRepository $userRepository): Response
{
    // Récupérer les utilisateurs groupés par rôle
    $usersByRole = $userRepository->countUsersByRole();

    // Préparer les données pour le graphique
    $labels = [];
    $data = [];
    foreach ($usersByRole as $row) {
        $labels[] = $row['role']; // Extraire le rôle
        $data[] = $row['count'];  // Extraire le compte
    }

    return $this->render('user/stats.html.twig', [
        'labels' => json_encode($labels), // Convertir en JSON pour JavaScript
        'data' => json_encode($data),    // Convertir en JSON pour JavaScript
    ]);
}


    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the uploaded photo
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                // Generate a unique filename for the uploaded image
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                
                try {
                    // Move the file to the directory where user photos are stored
                    $photoFile->move(
                        $this->getParameter('images_directory'), // Ensure this parameter is set in services.yaml
                        $newFilename
                    );
                    
                    // Set the filename in the user entity
                    $user->setPhoto($newFilename);
                } catch (\Exception $e) {
                    // Handle the error if file upload fails
                    $form->get('photo')->addError(new FormError('An error occurred while uploading the photo.'));
                }
            }

            // Persist the user data
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'button_label' => 'Create', // Button label for creating a user
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the uploaded photo if there's a new one
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                // Generate a unique filename for the uploaded image
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();

                try {
                    // Move the file to the directory where user photos are stored
                    $photoFile->move(
                        $this->getParameter('images_directory'), // Ensure this parameter is set in services.yaml
                        $newFilename
                    );
                    
                    // Set the filename in the user entity
                    $user->setPhoto($newFilename);
                } catch (\Exception $e) {
                    // Handle the error if file upload fails
                    $form->get('photo')->addError(new FormError('An error occurred while uploading the photo.'));
                }
            }

            // If no new photo, keep the existing photo
            // Persist the changes
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);}

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    

}
