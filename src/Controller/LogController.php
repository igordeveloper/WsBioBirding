<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Helper\AutenticateHelper;
use App\Entity\Log;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class LogController extends Controller
{


    public function lastSpeciesUpdate(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                if(empty($request->get("action")) OR $request->get("action") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[action] " . $translator->trans("nullArguments"));
                }

                $log = $this->getDoctrine()->getRepository(Log::class)->findLastSpeciesUpdate($request->get("action"));

                return new JsonResponse([
                            "authorized" => true,
                            "timestamp" => $log->getTimestamp()
                            ]);
            }else{
                return new JsonResponse(["authorized" => false]); 
            }
        }catch(\Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(\Doctrine\DBAL\DBALException $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }catch(Symfony\Component\Debug\Exception\FatalThrowableError | \TypeError  $ex){
            return new JsonResponse(["exception" => $ex->getMessage()]);
        }
    }
}