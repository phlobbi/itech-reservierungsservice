<?php

namespace App\Repository;

use App\Entity\RestaurantRatingCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RestaurantRatingCode>
 *
 * @method RestaurantRatingCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantRatingCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantRatingCode[]    findAll()
 * @method RestaurantRatingCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRatingCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RestaurantRatingCode::class);
    }

    public function findByCode(string $code): ?RestaurantRatingCode
    {
        return $this->findOneBy(['code' => $code]);
    }

//    /**
//     * @return RestaurantRatingCode[] Returns an array of RestaurantRatingCode objects
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

//    public function findOneBySomeField($value): ?RestaurantRatingCode
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
