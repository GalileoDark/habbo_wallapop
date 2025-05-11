<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrarFormType;
use App\Form\RegistrationFormType;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class UsuarioController extends AbstractController
{
    #[Route('/registrar', name: 'registrar')]
    public function register(Request $request,UsuarioRepository $usuarioRepository,UserPasswordHasherInterface $passwordHasher ): Response
    {
        // Crear nuevo usuario vacío
        $usuario = new Usuario();
        $usuario->setPais('No especificado');
        $usuario->setFotoPerfil('defaul.jpg');
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
}
