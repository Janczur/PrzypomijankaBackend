<?php

namespace App\Modules\Remembrall\Repository;

use App\Modules\Remembrall\Entity\PreReminder;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PreReminder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PreReminder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PreReminder[]    findAll()
 * @method PreReminder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreReminderRepository extends ServiceEntityRepository implements PreReminderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreReminder::class);
    }

    /**
     * @inheritDoc
     */
    public function getAllPreRemindersToBeSendBetween(DateTimeInterface $from, DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('pr')
            ->join('pr.reminder', 'reminder')
            ->andWhere('reminder.active = 1')
            ->andWhere('pr.remind_at BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult();
    }
}
