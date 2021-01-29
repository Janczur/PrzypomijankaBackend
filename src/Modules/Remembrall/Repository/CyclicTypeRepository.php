<?php

namespace App\Modules\Remembrall\Repository;

use App\Modules\Remembrall\Entity\CyclicType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CyclicType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CyclicType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CyclicType[]    findAll()
 * @method CyclicType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CyclicTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CyclicType::class);
    }

    // /**
    //  * @return CyclicType[] Returns an array of CyclicType objects
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
    public function findOneBySomeField($value): ?CyclicType
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
