<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Helper\AutenticateHelper;
use App\Entity\PopularName;
use App\Entity\Species;
use App\Entity\Log;
use Symfony\Component\Translation\TranslatorInterface;


class PopularNameController extends Controller
{

    public function insert(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){

                if(empty($request->get("species")) OR $request->get("species") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[species] " . $translator->trans("nullArguments"));
                }

                if(empty($request->get("name")) OR $request->get("name") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[name] " . $translator->trans("nullArguments"));
                }               

                $entityManager = $this->getDoctrine()->getManager();
                $species = $entityManager->getRepository(Species::class)->find($request->get('species'));


                if(!$species){
                    throw new \Doctrine\ORM\ORMInvalidArgumentException($translator->trans("invalid_specie"));
                }

                $popularName = new PopularName();
                $popularName->setSpecies($species);
                $popularName->setName($request->get('name'));

                $entityManager->persist($species);
                $entityManager->persist($popularName);
                $entityManager->flush();


                $log = new Log();
                $log->setAction('species');
                $log->setTimestamp(time());
                $entityManager->persist($log);
                $entityManager->flush();


                return new JsonResponse([
                            "authorized" => true,
                            "status" => true
                            ]);
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\ORM\ORMInvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex){
            return new JsonResponse(["exception" => $translator->trans("popular_duplicate_entry")]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function selectAllFromSpecies(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){

                if(empty($request->get("species")) OR $request->get("species") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[species] " . $translator->trans("nullArguments"));
                }

                $popularName = $this->getDoctrine()->getRepository(PopularName::class)->findBy(['species' => $request->get('species')]);


                $lista = array();
                foreach ($popularName as $name) {
                    $lista[] = array(
                                    'species' => $name->getSpecies()->getId(), 
                                    'name' => $name->getName()
                                    );         
                }
                return new JsonResponse([
                                'authorized' => true ,
                                'popularNames' => $lista
                                ]);
            }else{
                return new JsonResponse(["authorized" => false]); 
            }

        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function select(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){

                if(empty($request->get("species")) OR $request->get("species") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[species] " . $translator->trans("nullArguments"));
                }

                if(empty($request->get("name")) OR $request->get("name") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[name] " . $translator->trans("nullArguments"));
                } 

                $popularName = $this->getDoctrine()->getRepository(PopularName::class)
                                ->find(['species' => $request->get('species'), 'name' => $request->get('name')]);


                if($popularName){
                    $lista = array(
                            'species' => $popularName->getSpecies()->getId(), 
                            'name' => $popularName->getName(),
                            );  
                }else{
                    throw new \Doctrine\ORM\ORMException($translator->trans("invalid_popularName"));   
                }

                return new JsonResponse([
                                'authorized' => true,
                                'popularName' => $lista
                                ]);
            }else{
                return new JsonResponse(["authorized" => false]); 

            }
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function selectAll(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                $popularNames = $this->getDoctrine()->getRepository(PopularName::class)
                            ->findAll();

                $list = array();

                foreach ($popularNames as $popularName) {

                    $list[] = array(
                                'species' => $popularName->getSpecies()->getId(), 
                                'name' => $popularName->getName(),
                                'scientificName' => $popularName->getSpecies()->getScientificName(),
                                );         
                }

                return new JsonResponse(["authorized" => true , "popularNames" => $list]);
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function update(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){

                if(empty($request->get("species")) OR $request->get("species") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[species] " . $translator->trans("nullArguments"));
                }

                if(empty($request->get("name")) OR $request->get("name") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[name] " . $translator->trans("nullArguments"));
                } 

                if(empty($request->get("newName")) OR $request->get("newName") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[newName] " . $translator->trans("nullArguments"));
                } 


                $entityManager = $this->getDoctrine()->getManager();
                $popularName = $entityManager->getRepository(PopularName::class)
                                ->findOneBy([
                                'species' => $request->get('species'),
                                'name' => $request->get('name')
                                ]);

                if(!$popularName) {
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
                }else{
                    $popularName->setName($request->get('newName'));
                    $entityManager->flush();

                    $log = new Log();
                    $log->setAction('species');
                    $log->setTimestamp(time());
                    $entityManager->persist($log);
                    $entityManager->flush();

                    return new JsonResponse([
                                    'authorized' => true ,
                                    'status' => true
                                    ]);
                }
            }else{
                return new JsonResponse(['authorized' => false]); 
            }
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex){
            return new JsonResponse(["exception" => $translator->trans("popularName_duplicate_entry")]);
        }catch(\TypeError | \Doctrine\DBAL\Exception\InvalidArgumentException | \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function delete(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){
            

                if(empty($request->get("species")) OR $request->get("species") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[species] " . $translator->trans("nullArguments"));
                }

                if(empty($request->get("name")) OR $request->get("name") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[name] " . $translator->trans("nullArguments"));
                } 


                $entityManager = $this->getDoctrine()->getManager();
                $popularName = $entityManager->getRepository(PopularName::class)
                                ->findOneBy([
                                'species' => $request->get('species'),
                                'name' => $request->get('name')
                                ]);

                if(!$popularName) {
                    throw new \Doctrine\ORM\ORMException($translator->trans("invalid_popularName"));
                }else{
                    $entityManager->remove($popularName);
                    $entityManager->flush();

                    $log = new Log();
                    $log->setAction('species');
                    $log->setTimestamp(time());
                    $entityManager->persist($log);
                    $entityManager->flush();

                    return new JsonResponse([
                                    'authorized' => true,
                                    'status' => true
                                    ]);
                }
            }else{
                return new JsonResponse(['authorized' => false]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }

}