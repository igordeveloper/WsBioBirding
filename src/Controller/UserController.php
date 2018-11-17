<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Helper\AutenticateHelper;
use App\Entity\User;
use App\Entity\AccessLevel;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;


class UserController extends Controller
{

    public function insert(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator, \Swift_Mailer $mailer)
    {
        
        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                if( empty($request->get("password")) || $request->get("password") == NULL){
                    throw new \TypeError("NULL password");
                }

                if( empty($request->get("accessLevel")) || $request->get("accessLevel") == NULL){
                    throw new \TypeError("NULL accessLevel");
                }

                if( empty($request->get("fullName")) || $request->get("fullName") == NULL){
                    throw new \TypeError("NULL fullName");
                }

                if( empty($request->get("rg")) || $request->get("rg") == NULL){
                    throw new \TypeError("NULL rg");
                }

                if( empty($request->get("email")) || $request->get("email") == NULL){
                    throw new \TypeError("NULL email");
                }

                if( empty($request->get("nickname")) || $request->get("nickname") == NULL){
                    throw new \TypeError("NULL nickname");
                }

                $entityManager = $this->getDoctrine()->getManager();
                $accessLevel = $entityManager->getRepository(AccessLevel::class)->find($request->get("accessLevel"));

                $user = new User();
                $user->setRg($request->get("rg"))
                        ->setFullName($request->get("fullName"))
                        ->setEmail($request->get("email"))
                        ->setNickName($request->get("nickname"))
                        ->setPassword(hash("sha256", $request->get("password")))
                        ->setCrBio(empty($request->get("crBio")) || $request->get("crBio") == NULL ? NULL : $request->get("crBio"))
                        ->setAccessLevel($accessLevel)
                        ->setEnabled(true);

                $entityManager->persist($user);
                $entityManager->flush();

                $message = (new \Swift_Message("BioBirding"))
                    ->setFrom("igor.kusmitsch@gmail.com")
                    ->setTo($request->get("email"))
                    ->setBody(
                        $this->renderView( "emails/new_user.html.twig",
                                            array(
                                                "fullName" => $request->get("fullName"),
                                                "nickname" => $request->get("nickname"),
                                                "password" => $request->get("password")
                                            )
                        ),
                        "text/html"
                );

                $mailer->send($message);

                return new JsonResponse(["authorized" => true, "status" => true]);
            }else{
                return new JsonResponse(["authorized" => false]);

            }
        }catch(\TypeError $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }

    }


    public function update(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                if( empty($request->get("accessLevel")) || $request->get("accessLevel") == NULL){
                    throw new \TypeError("NULL accessLevel");
                }

                if( empty($request->get("fullName")) || $request->get("fullName") == NULL){
                    throw new \TypeError("NULL fullName");
                }

                if( empty($request->get("rg")) || $request->get("rg") == NULL){
                    throw new \TypeError("NULL rg");
                }

                if( empty($request->get("enabled")) || $request->get("enabled") == NULL){
                    throw new \TypeError("NULL enabled");
                }


                $entityManager = $this->getDoctrine()->getManager();
                $user = $this->getDoctrine()->getRepository(User::class)->findByRg($request->get("rg"));

                if($user){

                    $user->setFullName($request->get("fullName"))
                        ->setCrBio(empty($request->get("crBio")) || $request->get("crBio") == NULL ? NULL : $request->get("crBio"))
                        ->setAccessLevel($accessLevel)
                        ->setEnabled($request->get("enabled"));

                    $entityManager->flush();


                    return new JsonResponse(["authorized" => true, "status" => true]);

                }else{
                    throw new \Doctrine\ORM\ORMException($translator->trans("invalid_identifier"));
                    
                }   
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\TypeError $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex){
            return new JsonResponse(["exception" => $translator->trans("species_duplicate_entry")]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function select(Request $request, AutenticateHelper $autenticate){
        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                if( empty($request->get("rg")) || $request->get("rg") == NULL){
                    throw new \TypeError("NULL rg");
                }

                $user = $this->getDoctrine()->getRepository(User::class)->findByRg($request->get("rg"));

                if($user){
                    $list = array(
                        "fullName" => $user->getFullName(),
                        "rg" => $user->getRg(),
                        "nickname" => $user->getNickName(),
                        "email" => $user->getEmail(),
                        "enabled" => $user->getEnabled(),
                        "accessLevel" => $user->getAccessLevel()->getAccessLevel(),
                        "crBio" => empty($user->getCrBio()) ? "" : $species->getCrBio()
                    );
                }else{
                    throw new \Doctrine\ORM\ORMException($translator->trans("invalid_user"));
                    
                }

                return new JsonResponse(["authorized" => true , "user" => $list]);
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\TypeError $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }

    public function selectAll(Request $request, AutenticateHelper $autenticate)
    {
        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){
                $users = $this->getDoctrine()->getRepository(User::class)->findAll();

                $list = array();

                foreach ($users as $user) {

                    $list[] = array(
                        "rg" => $user->getRg(), 
                        "fullName" => $user->getFullName(),
                        "accessLevel" => $user->getAccessLevel()->getAccessLevel()
                    );         
                }

                return new JsonResponse(["authorized" => true , "users" => $list]);
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }

    }


    public function validate(Request $request)
    {
        
        try{
            $user = $this->getDoctrine()->getRepository(User::class)
                    ->findByEmailOrNickName($request->headers->get("nickname"), $request->headers->get("password"));

            if(!$user){
                return new JsonResponse(["notFound" => true]);
            }else{
                $userInfo = [];
                $userInfo["fullName"] = $user->getFullName();
                $userInfo["email"] = $user->getEmail();
                $userInfo["nickname"] = $user->getNickName();
                $userInfo["accessLevel"] = $user->getAccessLevel()->getAccessLevel();
                $userInfo["rg"] = $user->getRg();

                return new JsonResponse(["authorized" => true, "userInfo"=>$userInfo]);
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException | \Doctrine\DBAL\Exception\InvalidArgumentException$ex){
            return new JsonResponse(["exception" => $ex->getmessage()]);
        }
    }


    public function updatePassword(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {
        
        try{
        
            $user = $this->getDoctrine()->getRepository(User::class)
                ->findByEmailOrNickName($request->headers->get("nickname"), $request->headers->get("password"));

            if($user){  

                if(empty($request->headers->get("newPassword")) OR $request->headers->get("newPassword") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[newPassword] " . $translator->trans("nullArguments"));
                }

                if(empty($request->get("rg")) OR $request->get("rg") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[rg] " . $translator->trans("nullArguments"));
                }

                $user = $this->getDoctrine()->getRepository(User::class)->findByRg($request->get("rg"));

                if(!$user){
                    throw new \Doctrine\ORM\ORMException($translator->trans("invalid_user"));
                }else{
                    
                    $user->setPassword($request->headers->get("newPassword"));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->flush();

                    return new JsonResponse(["authorized" => true, "status" => true]);
                }
            }else{
                return new JsonResponse(["authorized" => false]);
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }

    }

    public function recoverPassword(Request $request, \Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        
        try{
            $user = $this->getDoctrine()->getRepository(User::class)->findByEmail($request->headers->get("email"));

            if(!$user){
                return new JsonResponse(["notFound" => true]);
            }else{

                $password = bin2hex(random_bytes(5));
                $user->setPassword(hash("sha256", $password));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $message = (new \Swift_Message("BioBirding"))
                    ->setFrom("igor.kusmitsch@gmail.com")
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView( "emails/new_password.html.twig",
                                            array(
                                                "name" => $user->getFullName(),
                                                "password" => $password
                                            )
                        ),
                        "text/html"
                );

                $mailer->send($message);

                return new JsonResponse(["authorized" => true, "status" => true ]);
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }
}