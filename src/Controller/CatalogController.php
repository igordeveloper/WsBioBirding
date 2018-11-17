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


    public function selectByIdentificationCode(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {
        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                $user = $this->getDoctrine()->getRepository(User::class)->find($request->get("rg"));
                $species = $this->getDoctrine()->getRepository(Species::class)->find($request->get("species"));

                if($request->get('accessLevel') < 3){
                    $catalog = $this->getDoctrine()->getRepository(Catalog::class)
                                ->selectByIdentificationCode($request->get("identificationCode"),
                                        $species);
                }else{
                    $catalog = $this->getDoctrine()->getRepository(Catalog::class)
                                ->selectByIdentificationCodeRg($request->get("identificationCode"),
                                        $species, $user);
                }


                if($catalog){

                    foreach ($catalog as $value) {
                        $list[] = array(
                            "id" => $value->getId(), 
                            "identificationCode" => $value->getIdentificationCode()
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
}