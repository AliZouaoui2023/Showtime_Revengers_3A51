<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontController extends AbstractController
{
    #[Route('/front', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('Front/base.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

    #[Route('/frontt', name: 'app_frontt')]
    public function indext(): Response
    {
        return $this->render('Front/mabase.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    
}
