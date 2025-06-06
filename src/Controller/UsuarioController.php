<?php

namespace App\Controller;

use App\Entity\Inventario;
use App\Entity\Objeto;
use App\Entity\Usuario;
use App\Form\RegistrarFormType;
use App\Form\UsuarioEditarType;
use App\Repository\InventarioRepository;
use App\Repository\ObjetoRepository;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;


class UsuarioController extends AbstractController
{
    #[Route('/registrar', name: 'registrar')]
    public function register(Request $request,UsuarioRepository $usuarioRepository,UserPasswordHasherInterface $passwordHasher ): Response
    {
        // Crear nuevo usuario vacío
        $usuario = new Usuario();
        $usuario->setPais('No especificado');
        $usuario->setFotoPerfil('defaul.png');
        $usuario->setDescripcion('');

        // Crear formulario
        $form = $this->createForm(RegistrarFormType::class, $usuario);
        $form->handleRequest($request);

        // Si formulario enviado y válido
        if ($form->isSubmitted() && $form->isValid()) {
            //Hashear la contraseña
            $hashedPassword = $passwordHasher->hashPassword(
                $usuario,
                $usuario->getPassword()
            );
            $usuario->setPassword($hashedPassword);
            $usuarioRepository->add($usuario, true);
            $this->addFlash('success','Usuario registrado con exito');
            return $this->redirectToRoute('login');
        }
        return $this->render('usuario/registrar.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $autentificacion): Response
    {
        $error = $autentificacion->getLastAuthenticationError();

        $ultimoUserName = $autentificacion->getLastUsername();

        return $this->render('usuario/login.html.twig', [
            'ultimoUserName' => $ultimoUserName,
            'error' => $error,
        ]);
    }
    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        // Symfony maneja automáticamente el logout, no es necesario implementar nada
    }

    #[Route('/perfil', name: 'perfil')]
    public function perfil(): Response
    {
        // Obtiene el usuario logueado
        $usuario = $this->getUser();

        // Si no está logueado, redirige al login
        if (!$usuario) {
            return $this->redirectToRoute('login');
        }

        return $this->render('usuario/perfil.html.twig', [
            'usuario' => $usuario,
        ]);
    }
    #[Route('/perfil/editar', name: 'perfil_editar')]
    public function editarPerfil(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $usuario = $this->getUser();
        $form = $this->createForm(UsuarioEditarType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fotoFile = $form->get('fotoPerfil')->getData();

            if ($fotoFile) {
                $originalFilename = pathinfo($fotoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $fotoFile->guessExtension();

                try {
                    $fotoFile->move(
                        $this->getParameter('perfil_directory'), // lo definimos abajo
                        $newFilename
                    );
                } catch (FileException $e) {
                    // manejar el error si falla
                }

                $usuario->setFotoPerfil($newFilename);
            }

            $em->persist($usuario);
            $em->flush();

            return $this->redirectToRoute('perfil');
        }

        return $this->render('usuario/editar.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/inventario', name: 'ver_inventario')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function verInventario(InventarioRepository $inventarioRepository): Response
    {
        $usuario = $this->getUser(); // Usuario logueado

        $inventario = $inventarioRepository->findBy(['usuario' => $usuario]);

        return $this->render('usuario/verInventario.html.twig', [
            'inventario' => $inventario,
        ]);
    }

    #[Route('/inventario/anadir/{id}', name: 'anadir_al_inventario')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function anadirAlInventario(
        int $id,
        Request $request,
        ObjetoRepository $objetoRepository,
        InventarioRepository $inventarioRepository,
        EntityManagerInterface $em
    ): RedirectResponse {
        $usuario = $this->getUser();
        $referer = $request->headers->get('referer') ?? $this->generateUrl('inicio');

        $objeto = $objetoRepository->find($id);

        if (!$objeto) {
            $this->addFlash('error', 'El objeto no se pudo añadir porque no existe.');
            return $this->redirect($referer);
        }

        try {
            $inventario = $inventarioRepository->findOneBy([
                'usuario' => $usuario,
                'objeto' => $objeto,
            ]);

            if ($inventario) {
                $inventario->setCantidad($inventario->getCantidad() + 1);
            } else {
                $inventario = new Inventario();
                $inventario->setUsuario($usuario);
                $inventario->setObjeto($objeto);
                $inventario->setCantidad(1);
                $em->persist($inventario);
            }

            $em->flush();
            $this->addFlash('success', '¡Objeto añadido al inventario!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Ocurrió un error al añadir el objeto.');
        }

        return $this->redirect($referer);
    }
    #[Route('/inventario/{id}/add', name: 'inventario_add', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addCantidad(
        Objeto $objeto,
        EntityManagerInterface $em,
        InventarioRepository $inventarioRepo
    ): RedirectResponse {
        $user = $this->getUser();
        $entrada = $inventarioRepo->findOneBy(['usuario' => $user, 'objeto' => $objeto]);

        if ($entrada) {
            $entrada->setCantidad($entrada->getCantidad() + 1);
        } else {
            $entrada = new Inventario();
            $entrada->setUsuario($user);
            $entrada->setObjeto($objeto);
            $entrada->setCantidad(1);
            $em->persist($entrada);
        }

        $em->flush();
        $this->addFlash('success', 'Cantidad añadida correctamente.');

        return $this->redirectToRoute('ver_inventario');
    }

    #[Route('/inventario/{id}/remove', name: 'inventario_remove', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function removeCantidad(
        Objeto $objeto,
        EntityManagerInterface $em,
        InventarioRepository $inventarioRepo
    ): RedirectResponse {
        $user = $this->getUser();
        $entrada = $inventarioRepo->findOneBy(['usuario' => $user, 'objeto' => $objeto]);

        if ($entrada) {
            $cantidadActual = $entrada->getCantidad();
            if ($cantidadActual > 1) {
                $entrada->setCantidad($cantidadActual - 1);
            } else {
                $em->remove($entrada); // Elimina la entrada si la cantidad llega a 0
            }

            $em->flush();
            $this->addFlash('success', 'Cantidad actualizada.');
        } else {
            $this->addFlash('danger', 'No tienes este objeto en el inventario.');
        }

        return $this->redirectToRoute('ver_inventario');
    }
}
