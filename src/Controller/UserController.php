<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Utils\Autenticar;
use App\Entity\User;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends Controller
{

    public function insert(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){

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
                        ->setCrBio($request->get('cr_bio'));

                $entityManager->persist($user);
                $entityManager->flush();
                return new JsonResponse(['status' => $translator->trans('success'), 'response' => $translator->trans('insert')]);
            }else{
                return new JsonResponse(['status' => $translator->trans('error'), 'response' => $translator->trans('insert')]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException  $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }


    public function validate(Request $request, TranslatorInterface $translator)
    {

        try{
            $user = $this->getDoctrine()->getRepository(User::class)->findByEmailOrNickName($request->headers->get('nickname'), $request->headers->get('password'));

            if(!$user){
                throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
            }else{
                return new JsonResponse(['status' => $translator->trans('success'), 'response' => true]);
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException  | \Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }
}