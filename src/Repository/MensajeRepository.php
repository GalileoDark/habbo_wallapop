<?php

namespace App\Repository;

use App\Entity\Mensaje;
use App\Entity\Publicacion;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mensaje>
 *
 * @method Mensaje|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mensaje|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mensaje[]    findAll()
 * @method Mensaje[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MensajeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mensaje::class);
    }
    public function findMensajesPorPublicacionYUsuario(Publicacion $publicacion, Usuario $usuario, int $limit, int $offset): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.publicacion = :pub')
            ->andWhere('(m.emisor = :usuario OR m.receptor = :usuario)')
            ->setParameter('pub', $publicacion)
            ->setParameter('usuario', $usuario)
            ->orderBy('m.fechaEnvio', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countMensajesPorPublicacionYUsuario(Publicacion $publicacion, Usuario $usuario): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.publicacion = :pub')
            ->andWhere('(m.emisor = :usuario OR m.receptor = :usuario)')
            ->setParameter('pub', $publicacion)
            ->setParameter('usuario', $usuario)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function determinarReceptor(Publicacion $publicacion, Usuario $emisor): ?Usuario
    {
        $receptor = $publicacion->getUsuario();

        if ($emisor === $receptor) {
            $ultimoMensaje = $this->createQueryBuilder('m')
                ->where('m.publicacion = :pub')
                ->andWhere('m.receptor = :yo')
                ->setParameter('pub', $publicacion)
                ->setParameter('yo', $emisor)
                ->orderBy('m.fechaEnvio', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            return $ultimoMensaje?->getEmisor();
        }

        return $receptor;
    }
    public function findMensajesPorPublicacionYUsuarioDesc(Publicacion $publicacion, Usuario $usuario, int $limit, int $offset): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.publicacion = :pub')
            ->andWhere('(m.emisor = :usuario OR m.receptor = :usuario)')
            ->setParameter('pub', $publicacion)
            ->setParameter('usuario', $usuario)
            ->orderBy('m.fechaEnvio', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function obtenerConversacionesPorUsuario(Usuario $usuario): array
    {
        $mensajes = $this->createQueryBuilder('m')
            ->where('m.emisor = :usuario OR m.receptor = :usuario')
            ->setParameter('usuario', $usuario)
            ->orderBy('m.fechaEnvio', 'DESC')
            ->getQuery()
            ->getResult();

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

        return $conversaciones;
    }


//    /**
//     * @return Mensaje[] Returns an array of Mensaje objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mensaje
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
