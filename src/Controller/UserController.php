<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Helper\AutenticateHelper;
use App\Entity\User;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends Controller
{

    public function insert(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('nickname'), $request->headers->get('password'))){

                if( empty($request->get('password')) || $request->get('password') == NULL) {
                    throw new \TypeError("parameter [password]: " . $translator->trans('empty'));
                }

                $entityManager = $this->getDoctrine()->getManager();
                $user = new User();
                $user->setRg($request->get('rg'))
                        ->setFullName($request->get('full_name'))
                        ->setEmail($request->get('email'))
                        ->setNickName($request->get('nickname'))
                        ->setPassword(hash('sha256', $request->get('password')))
                        ->setCrBio($request->get('crBio'))
                        ->setAccessLevel($request->get('accessLevel'));

                $entityManager->persist($user);
                $entityManager->flush();
                return new JsonResponse(['authorized' => true, 'response' => $translator->trans('insert')]);
            }else{
                return new JsonResponse(['authorized' => false]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException  $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function validate(Request $request, TranslatorInterface $translator)
    {

        try{
            $user = $this->getDoctrine()->getRepository(User::class)->findByEmailOrNickName($request->headers->get('nickname'), $request->headers->get('password'));

            if(!$user){
                throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
            }else{

                $userInfo = [];
                $userInfo["fullName"] = $user->getFullName();
                $userInfo["emal"] = $user->getEmail();
                $userInfo["nickname"] = $user->getNickName();
                $userInfo["accessLevel"] = $user->getAccessLevel()->getAccessLevel();
                $userInfo["rg"] = $user->getRg();
                return new JsonResponse(['authorized' => true, 'userInfo'=>$userInfo]);
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function updateStatus(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('nickname'), $request->headers->get('password'))){

                $user = $this->getDoctrine()->getRepository(User::class)->findByRg($request->get('rg'));

                if(!$user){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
                }else{

                    $entityManager = $this->getDoctrine()->getManager();
                    $user->setStatus($request->get('status'));
                    $entityManager->flush();
                    
                    return new JsonResponse(['authorized' => true, 'response' => $translator->trans('update')]);

                }

                return new JsonResponse(['authorized' => false]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException  $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }

    }

}