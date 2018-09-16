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


                if( empty($request->get("scientificName")) || is_null($request->get("scientificName")) ){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('exception_type_error'));
                }


                $entityManager = $this->getDoctrine()->getManager();
                $species = $entityManager->getRepository(Species::class)
                            ->findByScientificName($request->get("scientificName"));

                if($species){
                    throw new \Doctrine\DBAL\DBALException($translator->trans('exception_duplicate_entry'));
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
                            "status" => true,
                            "message" => $translator->trans('insert')
                            ]);
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\Doctrine\DBAL\DBALException | \Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse([
                        "authorized" => true,
                        "status" => false,
                        "message" => $translator->trans('insert')
                        ]);
        }
    }


    public function search(Request $request, AutenticateHelper $autenticate)
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
        }catch(\TypeError | \Doctrine\ORM\ORMException $ex ){
            return new JsonResponse([
                        "authorized" => true,
                        "status" => false,
                        "message" => $ex->getMessage()
                        ]);
        }
    }


    public function select(Request $request, AutenticateHelper $autenticate)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

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
                    $list = NULL;
                }

                return new JsonResponse(["authorized" => true , "species" => $list]);
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\TypeError | \Doctrine\ORM\ORMException $ex ){
            return new JsonResponse([
                        "authorized" => true,
                        "status" => false,
                        "message" => $ex->getMessage()
                        ]);
        }
    }


    public function selectAll(Request $request, AutenticateHelper $autenticate)
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
        }catch(\TypeError $ex){
            return new JsonResponse(["exception" => $ex->getmessage()]);
        }
    }


    public function update(Request $request, AutenticateHelper $autenticate)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                $entityManager = $this->getDoctrine()->getManager();
                $species = $entityManager->getRepository(Species::class)
                            ->find($request->get("id"));

                if(!$species) {
                    return new JsonResponse(["authorized" => true, "response" => false]);
                }else{

                    $species->setScientificName($request->get("scientificName"));
                    $species->setNotes(empty($request->get("notes")) ? NULL : $request->get("notes"));
                    $species->setConservationState(empty($request->get("conservationState")) ? 
                                NULL : $request->get("conservationState"));

                    $entityManager->flush();

                    return new JsonResponse(["authorized" => true, "response" => true]);

                }
            }
        }catch(\TypeError |  \Doctrine\DBAL\Exception\UniqueConstraintViolationException | \Doctrine\DBAL\Exception\InvalidArgumentException | \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getmessage()]);
        }
    }

}