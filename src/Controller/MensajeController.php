<?php

namespace App\Controller;

use App\Entity\Mensaje;
use App\Entity\Publicacion;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MensajeController extends AbstractController
{
    #[Route('/mensaje/nuevo/{id}', name: 'mensaje_nuevo')]
    public function nuevo(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario) {
            return $this->redirectToRoute('login');
        }

        $repoPublicacion = $em->getRepository(Publicacion::class);
        $publicacion = $repoPublicacion->find($id);

        if (!$publicacion) {
            throw $this->createNotFoundException('Publicación no encontrada');
        }

        $repoMensaje = $em->getRepository(Mensaje::class);

        $pagina = 1;
        $limit = 4;
        $offset = ($pagina - 1) * $limit;

        $qb = $repoMensaje->createQueryBuilder('m');
        $qb->where('m.publicacion = :pub')
            ->andWhere('(m.emisor = :usuario OR m.receptor = :usuario)')
            ->setParameter('pub', $publicacion)
            ->setParameter('usuario', $usuario)
            ->orderBy('m.fechaEnvio', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $mensajes = $qb->getQuery()->getResult();

        $qbCount = $repoMensaje->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.publicacion = :pub')
            ->andWhere('(m.emisor = :usuario OR m.receptor = :usuario)')
            ->setParameter('pub', $publicacion)
            ->setParameter('usuario', $usuario);

        $totalMensajes = (int)$qbCount->getQuery()->getSingleScalarResult();
        $hayMasMensajes = $totalMensajes > $limit;

        if ($request->isMethod('POST')) {
            $contenido = trim($request->request->get('contenido', ''));

            if ($contenido !== '') {
                $mensaje = new Mensaje();
                $mensaje->setContenido($contenido);
                $mensaje->setFechaEnvio(new \DateTimeImmutable());
                $mensaje->setEmisor($usuario);
                $mensaje->setPublicacion($publicacion);

                // Lógica corregida para definir receptor dinámicamente
                $receptor = $publicacion->getUsuario();

                if ($usuario === $receptor) {
                    // Si el dueño de la publicación responde, buscamos al último emisor
                    $ultimoMensaje = $repoMensaje->createQueryBuilder('m')
                        ->where('m.publicacion = :pub')
                        ->andWhere('m.receptor = :yo')
                        ->setParameter('pub', $publicacion)
                        ->setParameter('yo', $usuario)
                        ->orderBy('m.fechaEnvio', 'DESC')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

                    if ($ultimoMensaje) {
                        $receptor = $ultimoMensaje->getEmisor();
                    } else {
                        $this->addFlash('danger', 'No se puede determinar el receptor del mensaje.');
                        return $this->redirectToRoute('publicaciones');
                    }
                }

                $mensaje->setReceptor($receptor);

                $em->persist($mensaje);
                $em->flush();

                $this->addFlash('success', 'Mensaje enviado correctamente.');

                return $this->redirectToRoute('mensaje_nuevo', ['id' => $id]);
            } else {
                $this->addFlash('danger', 'El contenido del mensaje no puede estar vacío.');
            }
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
        EntityManagerInterface $em
    ): Response {
        $usuario = $this->getUser();
        if (!$usuario) {
            return new Response('', 401);
        }

        $page = max(1, (int)$request->query->get('page', 1));
        $limit = 4;
        $offset = ($page - 1) * $limit;

        $repoPublicacion = $em->getRepository(Publicacion::class);
        $publicacion = $repoPublicacion->find($id);

        if (!$publicacion) {
            return new Response('', 404);
        }

        $repoMensaje = $em->getRepository(Mensaje::class);
        $qb = $repoMensaje->createQueryBuilder('m');
        $qb->where('m.publicacion = :pub')
            ->andWhere('(m.emisor = :usuario OR m.receptor = :usuario)')
            ->setParameter('pub', $publicacion)
            ->setParameter('usuario', $usuario)
            ->orderBy('m.fechaEnvio', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $mensajes = $qb->getQuery()->getResult();

        if (empty($mensajes)) {
            return new Response('', 204);
        }

        return $this->render('mensaje/_mensajes_parciales.html.twig', [
            'mensajes' => $mensajes,
        ]);
    }
    #[Route('/mensajes', name: 'mensajes_lista')]
    public function listaMensajes(EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();

        $qb = $em->createQueryBuilder();
        $qb->select('m')
            ->from(Mensaje::class, 'm')
            ->where('m.emisor = :usuario OR m.receptor = :usuario')
            ->setParameter('usuario', $usuario)
            ->orderBy('m.fechaEnvio', 'DESC');

        $mensajes = $qb->getQuery()->getResult();

        $conversaciones = [];
        foreach ($mensajes as $mensaje) {
            $emisorId = $mensaje->getEmisor()->getId();
            $receptorId = $mensaje->getReceptor()->getId();
            $otroUsuario = $emisorId === $usuario->getId()
                ? $mensaje->getReceptor()
                : $mensaje->getEmisor();

            $publicacion = $mensaje->getPublicacion();
            $key = $otroUsuario->getId() . '-' . $publicacion->getId();

            if (!isset($conversaciones[$key])) {
                $conversaciones[$key] = [
                    'usuario' => $otroUsuario,
                    'publicacion' => $publicacion,
                    'ultimoMensaje' => $mensaje,
                ];
            }
        }

        return $this->render('mensaje/mensajes.html.twig', [
            'conversaciones' => $conversaciones,
        ]);
    }


}
