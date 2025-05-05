<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ObjetoController extends AbstractController
{
    #[Route('/objeto', name: 'app_objeto')]
    public function index(): Response
    {
        return $this->render('objeto/inicio.html.twig', [
            'controller_name' => 'ObjetoController',
        ]);
    }
}
