<?php

namespace App\Helper;

use App\Entity\User;
use Doctrine\ORM\EntityManager;


class AutenticateHelper
{

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function verify(string $authorization): bool
    {

        $authorization = base64_decode($authorization);
        $temporary = explode("||", $authorization);

        $user = $this->em->getRepository(User::class)->checkToken($temporary[0], $temporary[1], $temporary[2]);
         if($user){
            return true;
         }else{
            return false;
         }
    }
}