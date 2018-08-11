<?php

namespace App\Repository;

use App\Entity\AccessLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AccessLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessLevel[]    findAll()
 * @method AccessLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessLevelRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AccessLevel::class);
    }

//    /**
//     * @return AccessLevel[] Returns an array of AccessLevel objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AccessLevel
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
