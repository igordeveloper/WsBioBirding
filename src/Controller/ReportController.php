<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Helper\AutenticateHelper;
use App\Entity\Catalog;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ReportController extends Controller
{


    public function send(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator, \Swift_Mailer $mailer)
    {

        try{
            if($autenticate->verify($request->headers->get("authorizationCode"))){

                if(empty($request->get("startDate")) OR $request->get("startDate") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[startDate] " . $translator->trans("nullArguments"));
                }

                if(empty($request->get("finishDate")) OR $request->get("finishDate") == NULL){
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException("[finishDate] " . $translator->trans("nullArguments"));
                }


                $user = $this->getDoctrine()->getRepository(User::class)->findByRg($request->get("rg"));
                if(!$user){
                    throw new \Doctrine\ORM\ORMException($translator->trans("invalid_user"));
                }else{
                    
                    $accessLevel = $user->getAccessLevel()->getAccessLevel(); 
                    if( $accessLevel== 5 || $accessLevel == 6){
                        $records = $this->getDoctrine()->getRepository(Catalog::class)->fullReport($request->get("startDate"), $request->get("finishDate"));
                    }else{
                        $records = $this->getDoctrine()->getRepository(Catalog::class)->report($request->get("startDate"), $request->get("finishDate"),$request->get("rg"));
                    }

                    if($records){


                        $file = "BioBirding Report - " . date('d_m_Y__h_i_s') . "_" . $user->getRg();
                        $csvPath = "reports/".$file.".csv";

                        $csvh = fopen($csvPath, 'w');
                        $data = array($translator->trans("biologist"),
                            $translator->trans("species"),
                            $translator->trans("age"),
                            $translator->trans("sex"),
                            $translator->trans("temperature"),
                            $translator->trans("humidity"),
                            $translator->trans("wind"),
                            $translator->trans("weather"),
                            $translator->trans("notes"),
                            $translator->trans("date"),
                            $translator->trans("identication_code"),
                            $translator->trans("neighborhood"),
                            $translator->trans("city"),
                            $translator->trans("state"),
                            $translator->trans("latitude"),
                            $translator->trans("longitude"),
                            );

                        fputcsv($csvh, $data);

                        foreach($records as $catalog) {
                            $data = array($catalog->getUser()->getFullName(),
                                $catalog->getSpecies()->getScientificName(),
                                $catalog->getAge(),
                                $catalog->getSex(),
                                $catalog->getTemperature(),
                                $catalog->getHumidity(),
                                $catalog->getWind(),
                                $catalog->getWeather(),
                                $catalog->getNotes(),
                                date_format($catalog->getDate(), 'd/m/Y h:i:s'),
                                $catalog->getIdentificationCode(),
                                $catalog->getNeighborhood(),
                                $catalog->getCity(),
                                $catalog->getState(),
                                $catalog->getLatitude(),
                                $catalog->getLongitude(),
                                );
                            fputcsv($csvh, $data);
                        }

                    fclose($csvh);

                    }


                    $message = (new \Swift_Message("BioBirding"))
                        ->attach(\ Swift_Attachment::fromPath($csvPath)->setFilename($file . ".csv"))
                        ->setFrom("igor.kusmitsch@gmail.com")
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView( "emails/new_report.html.twig",
                                                array("name" => $user->getFullName())
                            ),
                            "text/html"
                    );

                    $mailer->send($message);
                    //unlink($csvPath);

                    return new JsonResponse(["authorized" => true, "status" => true ]);

                    }

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