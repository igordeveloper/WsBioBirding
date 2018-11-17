<?php

namespace App\Repository;

use App\Entity\Catalog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Catalog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Catalog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Catalog[]    findAll()
 * @method Catalog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Catalog::class);
    }

//    /**
//     * @return Catalog[] Returns an array of Catalog objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
    * @return array[] Returns a array with select count
    */
    public function findCatalog($rg, $latitude, $longitude, $date): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->andWhere('c.user = :rg')
            ->andWhere('c.latitude = :latitude')
            ->andWhere('c.longitude = :longitude')
            ->andWhere('c.date = :date')
            ->setParameter('rg', $rg)
            ->setParameter('latitude', $latitude)
            ->setParameter('longitude', $longitude)
            ->setParameter('date', $date)       
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    /**
    * @return array[] Returns an array of Catalog objects
    */
    public function selectByIdentificationCode($identificationCode, $species): ?array
    {

        return $this->createQueryBuilder('c')
            ->andWhere('c.identificationCode = :identificationCode')
            ->andWhere('c.species = :species')
            ->setParameter('identificationCode', $identificationCode)
            ->setParameter('species', $species)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
    * @return array[] Returns an array of Catalog objects
    */
    public function selectByIdentificationCodeRg($identificationCode, $species, $user): ?array
    {

        return $this->createQueryBuilder('c')
            ->andWhere('c.identificationCode = :identificationCode')
            ->andWhere('c.species = :species')
            ->andWhere('c.user = :user')
            ->setParameter('identificationCode', $identificationCode)
            ->setParameter('species', $species)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
    * @return Catalog[] Returns an array of Catalog objects
    */
    public function fullReport($startDate, $finishDate): ?array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.date BETWEEN :startDate AND :finishDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('finishDate', $finishDate)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
    * @return Catalog[] Returns an array of Catalog objects
    */
    public function report($startDate, $finishDate, $rg): ?array
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :rg')
            ->andWhere('c.date BETWEEN :startDate AND :finishDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('finishDate', $finishDate)
            ->setParameter('rg', $rg)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
    * @return Returns an array of state group
    */
    public function stateGroup(): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('c.state')
            ->groupBy('c.state')
            ->getQuery()
            ->getResult()
        ;
    }

}
