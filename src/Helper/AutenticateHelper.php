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

    public function verify(string $authorization): bool
    {

        $authorization = base64_decode($authorization);
        $temporary = explode("||", $authorization);

        $user = $this->em->getRepository(User::class)->findByEmailOrNickName($temporary[0], $temporary[1]);
         if($user){
            return true;
         }else{
            return false;
         }
    }
}