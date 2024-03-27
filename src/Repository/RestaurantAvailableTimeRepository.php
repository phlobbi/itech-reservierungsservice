<?php

namespace App\Repository;

use App\Entity\RestaurantAvailableTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RestaurantAvailableTime>
 *
 * @method RestaurantAvailableTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantAvailableTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantAvailableTime[]    findAll()
 * @method RestaurantAvailableTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantAvailableTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RestaurantAvailableTime::class);
    }

    /**
     * Get available times for a given date, number of guests and if the table is outside or not
     *
     * @param \DateTime $dateTime Date to check
     * @param int $guests Number of guests
     * @param bool $isOutside If the table is outside or not
     * @return array Array of available times
     */
    public function getAvailableTimes(\DateTime $dateTime, int $guests, bool $isOutside): array
    {
        $dateTime = $dateTime->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('rat')
            ->innerJoin('rat.restaurantTable', 'rt')
            ->leftJoin('rat.restaurantReserveration', 'rr')
            ->where('rat.date = :date')
            ->andWhere('rt.size >= :guests')
            ->andWhere('rt.isOutside = :isOutside')
            ->andWhere('rr.id IS NULL')
            ->setParameter('date', $dateTime)
            ->setParameter('guests', $guests)
            ->setParameter('isOutside', $isOutside);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get available times for a given date, time, number of guests and if the table is outside or not
     *
     * @param \DateTime $date Date to check
     * @param \DateTime $time Time to check
     * @param int $guests Number of guests
     * @param bool $isOutside If the table is outside or not
     * @return array Array of available times
     */
    public function getAvailableTimesWithTime(\DateTime $date, \DateTime $time, int $guests, bool $isOutside): array
    {
        $date = $date->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('rat')
            ->innerJoin('rat.restaurantTable', 'rt')
            ->leftJoin('rat.restaurantReserveration', 'rr')
            ->where('rat.date = :date')
            ->andWhere('rat.time = :time')
            ->andWhere('rt.size >= :guests')
            ->andWhere('rt.isOutside = :isOutside')
            ->andWhere('rr.id IS NULL')
            ->setParameter('date', $date)
            ->setParameter('time', $time)
            ->setParameter('guests', $guests)
            ->setParameter('isOutside', $isOutside);

        return $qb->getQuery()->getResult();
    }

    /**
     * Ruft alle Entitäten für das angegebene Datum ab.
     * @param \DateTime $date Datum, nach dem gesucht wird
     * @return array Array mit RestaurantAvailableTimes
     */
    public function getAvailableTimesForOneDay(\DateTime $date): array
    {
        $date = $date->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('rat')
            ->where('rat.date = :date')
            ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    /**
     * Ruft alle Zeiten ab, die vor dem angegebenen Datum liegen.
     *
     * @param \DateTime $date Datum, vor dem die Zeiten abgerufen werden
     * @return array Array mit den RestaurantAvailableTimes
     */
    public function findTimesBeforeDate(\DateTime $date): array
    {
        $qb = $this->createQueryBuilder('rat')
            ->where('rat.date < :date')
            ->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return RestaurantAvailableTime[] Returns an array of RestaurantAvailableTime objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RestaurantAvailableTime
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
