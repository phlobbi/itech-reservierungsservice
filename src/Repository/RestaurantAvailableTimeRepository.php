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
