<?php

namespace App\Repository;

use App\Entity\Publicacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publicacion>
 *
 * @method Publicacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publicacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publicacion[]    findAll()
 * @method Publicacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publicacion::class);
    }

    public function findUltimasPublicaciones(int $limite = 5): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.objeto', 'o')
            ->addSelect('o')
            ->leftJoin('o.categoria', 'c')
            ->addSelect('c')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult();
    }

    public function findLatest5(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC') // en vez de fechaCreacion
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

    }
}
