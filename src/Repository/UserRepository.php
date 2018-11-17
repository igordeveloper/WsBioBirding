<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }


    /**
     * @return User Returns a User object
     */
    public function checkToken(string $value, string $password, int $accessLevel)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.rg = :value')
            ->andWhere('u.password = :password')
            ->andWhere('u.enabled = :enabled')
            ->andWhere('u.access_level = :accessLevel')
            ->setParameter('value', $value)
            ->setParameter('password', $password)
            ->setParameter('enabled', 1)
            ->setParameter('accessLevel', $accessLevel)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @return User Returns a User object
     */
    public function findByEmailOrNickName(string $value, string $password)
    {
        return $this->createQueryBuilder('u')
            ->where('u.nickname = :value OR u.email = :value')
            ->andWhere('u.password = :password')
            ->setParameter('value', $value)
            ->setParameter('password', $password)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User Returns a User object
     */
    public function findByEmail(string $email)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :value')
            ->setParameter('value', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User Returns a User object
     */
    public function findByRg(string $rg)
    {
        return $this->createQueryBuilder('u')
            ->where('u.rg = :rg')
            ->setParameter('rg', $rg)
            ->getQuery()
            ->getOneOrNullResult();
    }




    
}
