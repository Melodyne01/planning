<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Activity;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

//    /**
//     * @return Activity[] Returns an array of Activity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Activity
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findAllByUser(User $user):array
    {
        return $this->createQueryBuilder('a')
        ->andWhere('a.createdBy = :user')
        ->setParameter('user', $user)
        ->orderBy('a.startedAt','DESC')
        ->getQuery()
        ->getResult();
    }
    
    public function findAllDailyByUser(DateTime $start, DateTime $end, User $user): array
    {
    return $this->createQueryBuilder('a')
        ->andWhere('a.createdBy = :user')
        ->andWhere('(a.startedAt BETWEEN :start AND :end) OR (a.endedAt BETWEEN :start AND :end)')
        ->setParameter('user', $user)
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->orderBy('a.startedAt', 'ASC')
        ->getQuery()
        ->getResult();
    }
}
