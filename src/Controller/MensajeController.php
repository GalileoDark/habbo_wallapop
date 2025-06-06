<?php

namespace App\Controller;

use App\Entity\Mensaje;
use App\Entity\Publicacion;
use App\Repository\MensajeRepository;
use App\Repository\PublicacionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MensajeController extends AbstractController
{
    #[Route('/mensaje/nuevo/{id}', name: 'mensaje_nuevo')]
    public function nuevo(int $id, Request $request, EntityManagerInterface $em, PublicacionRepository $publicacionRepository, MensajeRepository $mensajeRepository
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario) {
            return $this->redirectToRoute('login');
        }

        $publicacion = $publicacionRepository->find($id);
        if (!$publicacion) {
            throw $this->createNotFoundException('Publicación no encontrada');
        }

        $pagina = 1;
        $limit = 4;
        $offset = ($pagina - 1) * $limit;

        $mensajes = $mensajeRepository->findMensajesPorPublicacionYUsuario($publicacion, $usuario, $limit, $offset);
        $totalMensajes = $mensajeRepository->countMensajesPorPublicacionYUsuario($publicacion, $usuario);
        $hayMasMensajes = $totalMensajes > $limit;

        if ($request->isMethod('POST')) {
            $contenido = trim($request->request->get('contenido', ''));

            if ($contenido !== '') {
                $mensaje = new Mensaje();
                $mensaje->setContenido($contenido);
                $mensaje->setFechaEnvio(new \DateTimeImmutable());
                $mensaje->setEmisor($usuario);
                $mensaje->setPublicacion($publicacion);

                $receptor = $mensajeRepository->determinarReceptor($publicacion, $usuario);
                if (!$receptor) {
                    $this->addFlash('danger', 'No se puede determinar el receptor del mensaje.');
                    return $this->redirectToRoute('publicaciones');
                }

                $mensaje->setReceptor($receptor);
                $em->persist($mensaje);
                $em->flush();

                $this->addFlash('success', 'Mensaje enviado correctamente.');
                return $this->redirectToRoute('mensaje_nuevo', ['id' => $id]);
            }

            $this->addFlash('danger', 'El contenido del mensaje no puede estar vacío.');
        }

        return $this->render('mensaje/enviar.html.twig', [
            'publicacion' => $publicacion,
            'mensajes' => $mensajes,
            'hayMasMensajes' => $hayMasMensajes,
        ]);
    }
    #[Route('/mensaje/cargar/{id}', name: 'mensaje_cargar')]
    public function cargarMensajes(
        int $id,
        Request $request,
        PublicacionRepository $publicacionRepository,
        MensajeRepository $mensajeRepository
    ): Response {
        $usuario = $this->getUser();
        if (!$usuario) {
            return new Response('', 401);
        }

        $pagina = max(1, (int)$request->query->get('page', 1));
        $limit = 4;
        $offset = ($pagina - 1) * $limit;

        $publicacion = $publicacionRepository->find($id);
        if (!$publicacion) {
            return new Response('', 404);
        }

        $mensajes = $mensajeRepository->findMensajesPorPublicacionYUsuarioDesc($publicacion, $usuario, $limit, $offset);

        if (empty($mensajes)) {
            return new Response('', 204);
        }

        return $this->render('mensaje/_mensajes_parciales.html.twig', [
            'mensajes' => $mensajes,
        ]);
    }

    #[Route('/mensajes', name: 'mensajes_lista')]
    public function listaMensajes(MensajeRepository $mensajeRepository): Response
    {
        $usuario = $this->getUser();
        if (!$usuario) {
            return $this->redirectToRoute('login');
        }

        $conversaciones = $mensajeRepository->obtenerConversacionesPorUsuario($usuario);

        return $this->render('mensaje/mensajes.html.twig', [
            'conversaciones' => $conversaciones,
        ]);
    }


}
