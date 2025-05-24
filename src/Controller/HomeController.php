<?php

namespace App\Controller;

use App\Entity\Objeto;
use App\Repository\CategoriaRepository;
use App\Repository\ObjetoRepository;
use App\Repository\PublicacionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    #[Route('/', name: 'inicio')]
    public function index(ObjetoRepository $objetoRepository, PublicacionRepository $publicacionRepository): Response
    {
        $objetosTopPrecio = $objetoRepository->findObjetosConMayorPrecioMedio();
        $ultimasPublicaciones = $publicacionRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('home/inicio.html.twig', [
            'objetosTopPrecio' => $objetosTopPrecio,
            'ultimasPublicaciones' => $ultimasPublicaciones,
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
    public function objetosByCategoria(ObjetoRepository $objetoRepository,CategoriaRepository $categoriaRepository ,int $id)
    {
        $objetos = $objetoRepository->objetosByCategoria($id);
        $categoria = $categoriaRepository->find($id);
        $categoriaNombre = $categoria ? $categoria->getNombre() : 'default';
        return $this->render('home/objetosByCategoria.html.twig',[
            'objetos' => $objetos,
            "categoriaNombre" => $categoriaNombre
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
