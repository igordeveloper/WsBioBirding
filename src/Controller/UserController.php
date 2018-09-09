<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Helper\AutenticateHelper;
use App\Entity\User;
use App\Entity\AccessLevel;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends Controller
{

    public function insert(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){

                if( empty($request->get('password')) || $request->get('password') == NULL) {
                    throw new \TypeError("parameter [password]: " . $translator->trans('empty'));
                }

                $entityManager = $this->getDoctrine()->getManager();
                $accessLevel = $entityManager->getRepository(AccessLevel::class)->find($request->get('accessLevel'));

                $user = new User();
                $user->setRg($request->get('rg'))
                        ->setFullName($request->get('full_name'))
                        ->setEmail($request->get('email'))
                        ->setNickName($request->get('nickname'))
                        ->setPassword(hash('sha256', $request->get('password')))
                        ->setCrBio($request->get('crBio'))
                        ->setAccessLevel($accessLevel)
                        ->setEnabled(true);

                $entityManager->persist($user);
                $entityManager->flush();
                return new JsonResponse(['authorized' => true, 'response' => $translator->trans('insert')]);
            }else{
                return new JsonResponse(['authorized' => false]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException | \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function validate(Request $request, TranslatorInterface $translator)
    {

        try{
            $user = $this->getDoctrine()->getRepository(User::class)->findByEmailOrNickName($request->headers->get('nickname'), $request->headers->get('password'));

            if(!$user){
                return new JsonResponse(['notFound' => true]);
            }else{

                $userInfo = [];
                $userInfo["fullName"] = $user->getFullName();
                $userInfo["emal"] = $user->getEmail();
                $userInfo["nickname"] = $user->getNickName();
                $userInfo["accessLevel"] = $user->getAccessLevel()->getAccessLevel();
                $userInfo["rg"] = $user->getRg();
                return new JsonResponse(['authorized' => true, 'userInfo'=>$userInfo]);
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException | \Doctrine\DBAL\Exception\InvalidArgumentException$ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function updateStatus(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){

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
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException | \Doctrine\DBAL\Exception\InvalidArgumentException$ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }

    }

    public function newPassword(Request $request, \Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        try{
            $user = $this->getDoctrine()->getRepository(User::class)->findByEmail($request->headers->get('email'));

            if(!$user){
                throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
            }else{

                $password = bin2hex(random_bytes(5));
                $entityManager = $this->getDoctrine()->getManager();
                $user->setPassword(hash('sha256', $password));
                $entityManager->flush();

                $message = (new \Swift_Message('BioBirding'))
                    ->setFrom('igor.kusmitsch@gmail.com')
                    ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/new_password.html.twig',
                        array('name' => $user->getFullName(), 'password' => $password)
                    ),
                    'text/html'
                );

                $mailer->send($message);
                return new JsonResponse(['authorized' => true, 'response' => $translator->trans('sendPassword')]);
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException | \Doctrine\DBAL\Exception\InvalidArgumentException$ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }

}