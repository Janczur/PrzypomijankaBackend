<?php

namespace App\Modules\Remembrall\Repository;

use App\Modules\Remembrall\Entity\Cyclic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cyclic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cyclic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cyclic[]    findAll()
 * @method Cyclic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CyclicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cyclic::class);
    }

    // /**
    //  * @return Cyclic[] Returns an array of Cyclic objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cyclic
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
