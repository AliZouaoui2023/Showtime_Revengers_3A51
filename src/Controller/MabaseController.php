<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MabaseController extends AbstractController{
    #[Route('/mabase', name: 'app_mabase')]
    public function index(): Response
    {
        return $this->render('Front/mabase.html.twig', [
            'controller_name' => 'MabaseController',
        ]);
    }
}