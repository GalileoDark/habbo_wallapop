<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nombreUsuario = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pais = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fotoPerfil = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    // ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Obligatorio: identificador para login
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    // Password
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // Nombre de usuario
    public function getNombreUsuario(): ?string
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario(string $nombreUsuario): static
    {
        $this->nombreUsuario = $nombreUsuario;
        return $this;
    }

    // Pais
    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(?string $pais): static
    {
        $this->pais = $pais;
        return $this;
    }

    // Foto perfil
    public function getFotoPerfil(): ?string
    {
        return $this->fotoPerfil;
    }

    public function setFotoPerfil(string $fotoPerfil): static
    {
        $this->fotoPerfil = $fotoPerfil;
        return $this;
    }

    // Descripción
    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    // Roles obligatorios
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    // No usamos salt en Symfony moderno
    public function getSalt(): ?string
    {
        return null;
    }

    // Limpiar credenciales sensibles (si hubiera)
    public function eraseCredentials(): void
    {
        // No usamos nada extra aquí
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }
}
