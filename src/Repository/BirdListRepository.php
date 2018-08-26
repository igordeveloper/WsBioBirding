<?php

namespace App\Repository;

use App\Entity\BirdList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BirdList|null find($id, $lockMode = null, $lockVersion = null)
 * @method BirdList|null findOneBy(array $criteria, array $orderBy = null)
 * @method BirdList[]    findAll()
 * @method BirdList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BirdListRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BirdList::class);
    }

//    /**
//     * @return BirdList[] Returns an array of BirdList objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BirdList
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
