<?php

namespace App\Helper;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;


class AutenticateHelper
{


    private $container;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function verify(string $nickname, string $password): bool
    {

        $user = $this->em->getRepository(User::class)->findByEmailOrNickName($nickname, $password);
         if($user){
            return true;
         }else{
            return false;
         }
    }
}