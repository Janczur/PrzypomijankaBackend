<?php

namespace App\Modules\Remembrall\Repository;

use App\Modules\Remembrall\Entity\Reminder;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reminder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reminder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reminder[]    findAll()
 * @method Reminder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReminderRepository extends ServiceEntityRepository implements ReminderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reminder::class);
    }

    /**
     * @inheritDoc
     */
    public function getAllPreRemindersToBeSendBetween(DateTimeInterface $from, DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.pre_remind_at BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->andWhere('r.pre_reminded = 0')
            ->andWhere('r.active = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function getAllRemindersToBeSendBetween(DateTimeInterface $from, DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.remind_at BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->andWhere('r.active = 1')
            ->getQuery()
            ->getResult();
    }
}
