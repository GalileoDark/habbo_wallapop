<?php

namespace App\Controller;

use App\Repository\CategoriaRepository;
use App\Repository\ObjetoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'inicio')]
    public function index(): Response
    {
        return $this->render('home/inicio.html.twig', [
            'controller_name' => 'HomeController'
        ]);
    }
    #[Route('/categorias', name: 'categorias')]
    public function categorias(CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->todasLasCategorias();
        return $this->render('home/categorias.html.twig', [
            'categorias' => $categorias,
        ]);
    }
    #[Route('/objetos/{id}', name: 'objetoByCategoria')]
    public function objetosByCategoria(ObjetoRepository $objetoRepository ,int $id)
    {
        $objetos = $objetoRepository->objetosByCategoria($id);
        return $this->render('home/objetosByCategoria.html.twig',[
            'objetos' => $objetos
        ]);
    }
    #[Route('/publicaciones', name: 'publicaciones')]
    public function publicaciones(ObjetoRepository $objetoRepository ,int $id)
    {
        $objetos = $objetoRepository->objetosByCategoria($id);
        return $this->render('home/objetosByCategoria.html.twig',[
            'objetos' => $objetos
        ]);
    }
}
