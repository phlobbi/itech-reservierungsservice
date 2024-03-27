<?php

namespace App\Repository;

use App\Entity\RestaurantReserveration;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RestaurantReserveration>
 *
 * @method RestaurantReserveration|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantReserveration|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantReserveration[]    findAll()
 * @method RestaurantReserveration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantReserverationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RestaurantReserveration::class);
    }

    /**
     * Ruft alle Reservierungen für ein bestimmtes Datum ab
     * @param DateTime $date Datum, an dem gesucht werden soll
     * @return array Reservierungen
     */
    public function getReservationsForDate(DateTime $date): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.restaurantAvailableTime', 'rat')
            ->where('rat.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    /**
     * Ruft alle Reservierungen für ein bestimmtes Datum und eine bestimmte E-Mail-Adresse ab
     * @param DateTime $date Datum, an dem gesucht werden soll
     * @param string $email E-Mail-Adresse, nach der gesucht werden soll
     * @return array Reservierungen
     */
    public function getReservationsByDateAndEmail(DateTime $date, string $email): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.restaurantAvailableTime', 'rat')
            ->where('rat.date = :date')
            ->andWhere('r.email = :email')
            ->setParameter('date', $date)
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return RestaurantReserveration[] Returns an array of RestaurantReserveration objects
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

//    public function findOneBySomeField($value): ?RestaurantReserveration
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
