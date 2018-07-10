<?php

namespace App\Repository;

use App\Entity\PopularName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PopularName|null find($id, $lockMode = null, $lockVersion = null)
 * @method PopularName|null findOneBy(array $criteria, array $orderBy = null)
 * @method PopularName[]    findAll()
 * @method PopularName[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PopularNameRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PopularName::class);
    }

//    /**
//     * @return PopularName[] Returns an array of PopularName objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PopularName
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
