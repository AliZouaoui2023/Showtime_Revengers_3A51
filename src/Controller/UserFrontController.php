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
use App\Form\UserType;  // Ensure th
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\RequestStack;


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

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();  // This returns a User object, not an array
        $email = $data->getEmail();  // Use the getter for email
        $password = $data->getMotDePasse();  // Use the getter for password

        // Get the user repository
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            // Check if the password is correct
            if ($user->getMotDePasse() === $password) {
                // User credentials are correct, create session
                $session = $requestStack->getSession();
                $session->set('user', $user->getId());

                return $this->redirectToRoute('app_user_front'); // Redirect after successful login
            } else {
                // Invalid password
                $form->get('mot_de_passe')->addError(new FormError('Invalid password.'));
            }
        } else {
            // Invalid email
            $form->get('email')->addError(new FormError('User with this email does not exist.'));
        }
    }

    return $this->render('user_front/login_form.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony handles the logout automatically, so no logic needed here
    }
        

}
