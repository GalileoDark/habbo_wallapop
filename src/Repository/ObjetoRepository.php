<?php

namespace App\Repository;

use App\Entity\Objeto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Objeto>
 *
 * @method Objeto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Objeto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Objeto[]    findAll()
 * @method Objeto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObjetoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objeto::class);
    }

    // Buscar objetos por categoria
    public function objetosByCategoria($id)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('c') // esto asegura que también se cargue la categoría
            ->join('o.categoria', 'c')
            ->where('o.categoria = :categoria')
            ->setParameter('categoria', $id)
            ->getQuery()
            ->getResult();

    }
    //Busca el objeto con mayo rprecio medio
    public function findObjetosConMayorPrecioMedio(int $limite = 5): array
    {
        return $this->createQueryBuilder('o')
            ->join('o.categoria', 'c')
            ->orderBy('o.precioMedio', 'DESC')
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult();
    }
}
