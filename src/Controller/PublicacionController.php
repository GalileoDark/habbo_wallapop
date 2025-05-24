<?php
namespace App\Controller;

use App\Entity\Publicacion;
use App\Form\PublicacionFormType;
use App\Repository\PublicacionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PublicacionController extends AbstractController
{
    #[Route('/publicacion/nueva', name: 'publicacion_nueva')]
    public function nueva(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $publicacion = new Publicacion();

        // Asociar usuario logueado
        $usuario = $security->getUser();
        if (!$usuario) {
            $this->addFlash('error', 'Debes estar logueado para crear una publicación.');
            return $this->redirectToRoute('login');
        }
        $publicacion->setUsuario($usuario);

        $form = $this->createForm(PublicacionFormType::class, $publicacion);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($publicacion);
            $em->flush();

            $this->addFlash('success', 'Publicación creada correctamente.');
            return $this->redirectToRoute('inicio'); // Cambia por la ruta que prefieras
        }

        return $this->render('publicacion/crearPublicacion.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/publicaciones', name: 'publicaciones')]
    public function publicaciones(PublicacionRepository $repo): Response
    {
        $limite = 3;
        $pagina = 1;
        $offset = ($pagina - 1) * $limite;

        $publicaciones = $repo->findBy([], ['id' => 'DESC'], $limite, $offset);

        return $this->render('publicacion/publicaciones.html.twig', [
            'publicaciones' => $publicaciones,
        ]);
    }
    #[Route('/cargar-publicaciones', name: 'cargar_publicaciones')]
    public function cargarPublicaciones(Request $request, PublicacionRepository $repo): Response
    {
        $pagina = $request->query->getInt('page', 1);
        $limite = 3;
        $offset = ($pagina - 1) * $limite;

        $publicaciones = $repo->findBy([], ['id' => 'DESC'], $limite, $offset);

        return $this->render('publicacion/_publicacion_card.twig', [
            'publicaciones' => $publicaciones,
        ]);
    }
    #[Route('/publicacion/{id}', name: 'publicacion_ver', requirements: ['id' => '\d+'])]
    public function ver(Publicacion $publicacion): Response
    {
        return $this->render('publicacion/ver.html.twig', [
            'publicacion' => $publicacion,
        ]);
    }



}
