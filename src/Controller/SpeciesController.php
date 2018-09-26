<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Species;
use App\Helper\AutenticateHelper;
use App\Helper\WeatherHelper;
use Symfony\Component\Translation\TranslatorInterface;

class SpeciesController extends AbstractController
{

    public function insert(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                if(empty($request->get("scientificName")) OR $request->get("scientificName") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[scientificName] " . $translator->trans("nullArguments"));
                }

                $species = new Species();
                $species->setScientificName($request->get("scientificName"));
                $species->setNotes(empty($request->get("notes")) ? NULL : $request->get("notes"));
                $species->setConservationState(empty($request->get("conservationState")) ? 
                            NULL : $request->get("conservationState"));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($species);
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
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex){
            return new JsonResponse(["exception" => $translator->trans("species_duplicate_entry")]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function search(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                $species = $this->getDoctrine()->getRepository(Species::class)
                            ->findByScientificName($request->get("scientificName"));

                $list = array();

                foreach ($species as $specie) {

                    $list[] = array(
                        "id" => $specie->getId(), 
                        "scientificName" => $specie->getScientificName()
                    );

                }

                return new JsonResponse(["authorized" => true , "species" => $list]);
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\TypeError $ex){
            return new JsonResponse(["exception" => "[scientificName] " . $translator->trans("nullArguments")]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function select(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                if(empty($request->get("id")) OR $request->get("id") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[id] " . $translator->trans("nullArguments"));
                }

                $species = $this->getDoctrine()->getRepository(Species::class)
                            ->find($request->get("id"));
                
                if($species){
                    $list = array(
                        "scientificName" => $species->getScientificName(), 
                        "notes" => empty($species->getNotes()) ? "" : $species->getNotes(),
                        "conservationState" => empty($species->getConservationState()) ? "" :
                                                $species->getConservationState(),
                    );
                }else{
                    throw new \Doctrine\ORM\ORMException($translator->trans("invalid_specie"));
                    
                }

                return new JsonResponse(["authorized" => true , "species" => $list]);
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\TypeError $ex){
            return new JsonResponse(["exception" => $translator->trans("exception_type_error")]);
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function selectAll(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                $species = $this->getDoctrine()->getRepository(Species::class)
                            ->findAll();

                $list = array();

                foreach ($species as $specie) {

                    $list[] = array(
                        "id" => $specie->getId(), 
                        "scientificName" => $specie->getScientificName()
                    );         
                }

                return new JsonResponse(["authorized" => true , "species" => $list]);
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
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                if(empty($request->get("id")) OR $request->get("id") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[id] " . $translator->trans("nullArguments"));
                }

                $entityManager = $this->getDoctrine()->getManager();
                $species = $entityManager->getRepository(Species::class)
                            ->find($request->get("id"));

                if($species){

                    if(empty($request->get("scientificName")) OR $request->get("scientificName") == NULL){
                        throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[scientificName] " . $translator->trans("nullArguments"));
                    }

                    $species->setScientificName($request->get("scientificName"));
                    $species->setNotes(empty($request->get("notes")) ? NULL : $request->get("notes"));
                    $species->setConservationState(empty($request->get("conservationState")) ? 
                                NULL : $request->get("conservationState"));

                    $entityManager->flush();

                    return new JsonResponse(["authorized" => true, "status" => true]);

                }else{
                    throw new \Doctrine\ORM\ORMException($translator->trans("invalid_identifier"));
                    
                }   
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex){
            return new JsonResponse(["exception" => $translator->trans("species_duplicate_entry")]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }

}