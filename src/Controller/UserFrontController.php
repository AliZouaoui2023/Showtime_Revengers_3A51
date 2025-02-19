<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\LoginType;
use App\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class UserFrontController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    

    #[Route('/frontuser', name: 'app_user_front')]
    public function index(): Response
    {
        return $this->render('user_front/index.html.twig', [
            'controller_name' => 'UserFrontController',
        ]);
    }

    #[Route('/signup', name: 'frontend_signup', methods: ['GET', 'POST'])]
    public function signup(Request $request, EntityManagerInterface $entityManager): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);
    if ($form->isSubmitted()) {
        dump($request->request->all()); // Debug the submitted data
        dump($form->getData()); // Debug the form data after submission
        dump($form->getErrors(true, false)); // Debug form errors
    }

    if ($form->isSubmitted() && $form->isValid()) {
        $password = $form->get('mot_de_passe')->getData();
        $repassword = $request->request->get('repassword');

        if ($password !== $repassword) {
            $form->get('mot_de_passe')->addError(new FormError('Passwords do not match.'));
        } else {
            // Handle photo file upload
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                // Generate a unique filename for the uploaded image
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoFile->move(
                        $this->getParameter('uploads_directory'), // Ensure this parameter is defined in config/services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle the error, for example, add an error message
                    $form->get('photo')->addError(new FormError('An error occurred while uploading the photo.'));
                }

                // Store the file name (or path) in the entity
                $user->setPhoto($newFilename);
            }

            // Encrypt the password (if necessary)
            // $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            // $user->setMotDePasse($encodedPassword);

            // Persist the user data
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_front');
        }
    }

    return $this->render('user_front/signup_form.html.twig', [
        'form' => $form->createView(),
    ]);}

    #[Route('/login', name: 'frontend_login', methods: ['GET', 'POST'])]
public function login(Request $request, RequestStack $requestStack): Response
{
    // Create the login form
    $form = $this->createForm(LoginType::class);
    $form->handleRequest($request);

    // Debugging: Check submitted data and form errors
    dump($request->request->all()); // Debug the submitted data
    dump($form->getData()); // Debug the form data after submission
    dump($form->getErrors(true, false)); // Debug form errors

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData(); // Get the submitted form data (User object)
        $email = $data->getEmail(); // Extract email
        $password = $data->getMotDePasse(); // Extract password

        // Fetch the user from the database
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            // Check if the password matches
            if ($user->getMotDePasse() === $password) {
                // Set the user session
                $session = $requestStack->getSession();
                $session->set('user', $user->getId());

                // Debugging: Check if the session is set correctly
                dump($session->get('user'));

                // Redirect based on user role
                if ($user->getRole() === 'Admin') {
                    // Redirect to the admin page
                    return $this->redirectToRoute('app_user_index');
                } else {
                    // Redirect to the front-end user page
                    return $this->redirectToRoute('app_user_front');
                }
            } else {
                // Add an error if the password is incorrect
                $form->get('mot_de_passe')->addError(new FormError('Invalid password.'));
            }
        } else {
            // Add an error if the user does not exist
            $form->get('email')->addError(new FormError('User with this email does not exist.'));
        }

        // Clear the form data after handling submission
        $form = $this->createForm(LoginType::class); // Reset the form
    }

    // Render the login form template
    return $this->render('user_front/login_form.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony handles the logout automatically, so no logic needed here
    }

    #[Route('/my-account', name: 'app_user_account')]
    public function account(Request $request, EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        // Get the user ID from the session
        $session = $requestStack->getSession();
        $userId = $session->get('user'); // Retrieve the user ID from the session
    
        if ($userId) {
            // If the user ID is found in the session, fetch the user from the database
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->find($userId);
    
            if ($user) {
                // Render the account page and pass the user data to the template
                return $this->render('user_front/my_account.html.twig', [
                    'user' => $user
                ]);
            }
        }
    
        // If no user is found in session or database, redirect to the login page
        return $this->redirectToRoute('frontend_login');
    }

    #[Route('/front/{id}/edit', name: 'frontend_user_edit', methods: ['GET', 'POST'])]
public function editUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $photoFile */
        $photoFile = $form->get('photo')->getData();
        
        if ($photoFile) {
            // Generate a unique filename for the uploaded image
            $newFilename = uniqid() . '.' . $photoFile->guessExtension();

            try {
                // Move the file to the directory where user photos are stored
                $photoFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );

                // Set the filename in the user entity
                $user->setPhoto($newFilename);
            } catch (\Exception $e) {
                $form->get('photo')->addError(new FormError('An error occurred while uploading the photo.'));
            }
        }

        // Persist the changes to the database
        $entityManager->flush();

        // Redirect after a successful update
        return $this->redirectToRoute('app_user_front', [], Response::HTTP_SEE_OTHER);
    }

    // Render the template specifically for the front-end user edit page
    return $this->render('user_front/userfront_edit.html.twig', [
        'user' => $user,
        'form' => $form->createView(),
    ]);
}

        

}
