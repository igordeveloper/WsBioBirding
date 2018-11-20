<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Helper\AutenticateHelper;
use App\Helper\WeatherHelper;
use App\Helper\LocationHelper;
use App\Entity\Catalog;
use App\Entity\User;
use App\Entity\Species;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;


class CatalogController extends Controller
{


    public function insert(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator, LocationHelper $locationHelper)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){


                $weatherHelper = new WeatherHelper();
                $w = $weatherHelper->check($request->get("latitude"), $request->get("longitude"), $request->get("timestamp"));

                $locationHelper = new LocationHelper();
                $l = $locationHelper->check($request->get("latitude"), $request->get("longitude"));

                if($l["country"] == "Brasil"){

                    $user = $this->getDoctrine()->getRepository(User::class)->find($request->get("rg"));
                    $species = $this->getDoctrine()->getRepository(Species::class)->find($request->get("species"));

                    $dateImmutable = new \DateTime();
                    $dateImmutable->setTimestamp($request->get("timestamp"));


                    $catalog = new Catalog();
                    $catalog->setUser($user);
                    $catalog->setSpecies($species);
                    $catalog->setAge($request->get("age"));
                    $catalog->setSex($request->get("sex"));
                    $catalog->setLatitude($request->get("latitude"));
                    $catalog->setLongitude($request->get("longitude"));
                    $catalog->setTemperature($w["temperature"]);
                    $catalog->setHumidity($w["humidity"]);
                    $catalog->setWind($w["windSpeed"]);
                    $catalog->setWeather($w["weather"]);
                    $catalog->setDate($dateImmutable);
                    $catalog->setNotes(empty($request->get("notes")) ? NULL : $request->get("notes"));
                    $catalog->setIdentificationCode(empty($request->get("identificationCode")) ? NULL : $request->get("identificationCode"));
                    $catalog->setNeighborhood($l["neighborhood"]);
                    $catalog->setCity($l["city"]);
                    $catalog->setState($l["state"]);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($catalog);
                    $entityManager->flush();

                    return new JsonResponse([
                                "authorized" => true,
                                "status" => true
                                ]);
                }else{
                    return new JsonResponse([
                                "authorized" => true,
                                "status" => false
                                ]);
                }

            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex){
            return new JsonResponse(["exception" => $translator->trans("species_duplicate_entry")]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(Symfony\Component\Debug\Exception\FatalThrowableError | \TypeError  $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }
    }


    public function selectCount(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                $dateImmutable = new \DateTime();
                $dateImmutable->setTimestamp($request->get("timestamp"));

                $catalog = $this->getDoctrine()->getRepository(Catalog::class)
                            ->findCatalog($request->get("rg"),
                                        $request->get("latitude"),
                                        $request->get("longitude"),
                                        $dateImmutable);

                if($catalog){
                    return new JsonResponse(["authorized" => true , "count" => $catalog[1]]);
                }
            }else{
                return new JsonResponse(["authorized" => false]);  
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\TypeError $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function selectFilter(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {
        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){



                if($request->get("accessLevel") < 3){
                    $rg = NULL;
                }


                if(empty($request->get("state")) OR $request->get("state") == ""){
                    $state = NULL;
                }else{
                    $state = $request->get("state");
                }

                if(empty($request->get("city")) OR $request->get("city") == ""){
                    $city = NULL;
                }else{
                    $city = $request->get("city");
                }


                if(empty($request->get("identificationCode")) OR $request->get("identificationCode") == ""){
                    $identificationCode = NULL;
                }else{
                    $identificationCode = $request->get("identificationCode");
                }


                if(empty($request->get("species")) OR $request->get("species") == ""){
                    $species = NULL;
                }else{
                    $species = $request->get("species");
                }


                $startDate = $request->get("startDate") . " 00:00:00";
                $finishDate = $request->get("finishDate") . " 23:59:59";


                $catalog = $this->getDoctrine()->getRepository(Catalog::class)
                                ->selectFilter($rg,
                                            $state,
                                            $city,
                                            $identificationCode,
                                            $startDate,
                                            $finishDate,
                                            $species);

                if($catalog){

                    foreach ($catalog as $value) {



                        $list[] = array(
                            "id" => $value->getId(),
                            "state" => $value->getState(),
                            "city" => $value->getCity(),
                            "age" => $value->getAge(),
                            "sex" => $value->getSex(),
                            "species" => $value->getSpecies()->getScientificName(),
                            "date" => date_format($value->getDate(), 'd/m/Y H:i:s')
                        );
                    }
                    return new JsonResponse(["authorized" => true , "list" => $list]);
                }else{
                    return new JsonResponse(["authorized" => true , "list" => NULL]);
                }

            }else{
                
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\TypeError $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }
    }



    public function select(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                if(empty($request->get("id")) OR $request->get("id") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[id] " . $translator->trans("nullArguments"));
                }

                $catalog = $this->getDoctrine()->getRepository(Catalog::class)
                            ->find($request->get("id"));
                
                if($catalog){
                    $list = array(
                        "id" => $catalog->getId(),
                        "species" => $catalog->getSpecies()->getId(),
                        "latitude" => $catalog->getLatitude(),
                        "longitude" => $catalog->getLongitude(), 
                        "age" => $catalog->getAge(),
                        "sex" => $catalog->getSex(),
                        "temperature" => $catalog->getTemperature(),
                        "humidity" => $catalog->getHumidity(),
                        "wind" => $catalog->getWind(),
                        "weather" => $catalog->getWeather(),
                        "notes" => empty($catalog->getNotes()) ? "" : $catalog->getNotes(),
                        "identificationCode" => empty($catalog->getIdentificationCode()) ? "" : $catalog->getIdentificationCode(),
                        "neighborhood" => empty($catalog->getNeighborhood()) ? "" : $catalog->getNeighborhood(),
                        "city" => empty($catalog->getCity()) ? "" : $catalog->getCity(),
                        "state" => empty($catalog->getState()) ? "" : $catalog->getState()
                    );
                }else{
                    throw new \Doctrine\ORM\ORMException($translator->trans("invalid_catalog"));
                    
                }

                return new JsonResponse(["authorized" => true , "catalog" => $list]);
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


    public function selectStateGroup(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {
        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                $catalog = $this->getDoctrine()->getRepository(Catalog::class)->stateGroup();

                if($catalog){

                    foreach ($catalog as $value) {
                        $list[] = array(
                            "state" => $value['state']
                        );
                    }
                    return new JsonResponse(["authorized" => true , "list" => $list]);
                }else{
                    return new JsonResponse(["authorized" => true , "list" => NULL]);
                }

            }else{
                
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\TypeError $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $translator->trans("DBALException")]);
        }
    }


    public function selectCityGroup(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {
        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                $catalog = $this->getDoctrine()->getRepository(Catalog::class)->cityGroup($request->get("state"));

                if($catalog){

                    foreach ($catalog as $value) {
                        $list[] = array(
                            "city" => $value['city']
                        );
                    }
                    return new JsonResponse(["authorized" => true , "list" => $list]);
                }else{
                    return new JsonResponse(["authorized" => true , "list" => NULL]);
                }

            }else{
                
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\TypeError $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
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

                if(empty($request->get("species")) OR $request->get("species") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[species] " . $translator->trans("nullArguments"));
                }

                $entityManager = $this->getDoctrine()->getManager();
                $catalog = $entityManager->getRepository(Catalog::class)
                            ->find($request->get("id"));
                $species = $this->getDoctrine()->getRepository(Species::class)
                            ->find($request->get("species"));

                if($catalog){

                    $catalog->setSpecies($species);
                    $catalog->setAge($request->get("age"));
                    $catalog->setSex($request->get("sex"));
                    $catalog->setLatitude($request->get("latitude"));
                    $catalog->setLongitude($request->get("longitude"));
                    $catalog->setTemperature($request->get("temperature"));
                    $catalog->setHumidity($request->get("humidity"));
                    $catalog->setWind($request->get("windSpeed"));
                    $catalog->setWeather($request->get("weather"));
                    $catalog->setNotes(empty($request->get("notes")) ? NULL : $request->get("notes"));
                    $catalog->setIdentificationCode(empty($request->get("identificationCode")) ? NULL : $request->get("identificationCode"));
                    $catalog->setNeighborhood($request->get("neighborhood"));
                    $catalog->setCity($request->get("city"));
                    $catalog->setState($request->get("state"));

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
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\ORM\ORMException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }
    }

    public function delete(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){
            

                if(empty($request->get("id")) OR $request->get("id") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[id] " . $translator->trans("nullArguments"));
                }


                $entityManager = $this->getDoctrine()->getManager();
                $catalog = $entityManager->getRepository(Catalog::class)
                            ->find($request->get("id"));

                if(!$catalog) {
                    throw new \Doctrine\ORM\ORMException($translator->trans("invalid_catalog"));
                }else{
                    $entityManager->remove($catalog);
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