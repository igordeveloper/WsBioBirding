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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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


                        $file = "BioBirding Report - " . date('d_m_Y__h_i_s') . "_" . $user->getRg() . ".xlsx";

                        $spreadsheet = new Spreadsheet();

                        $c = 1;

                        $spreadsheet->getActiveSheet()->getStyle('A1:P1')->getFill()
                                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                    ->getStartColor()->setRGB('004d8b');
                        $spreadsheet->getActiveSheet()->getStyle('A1:P1')
                                    ->getFont()->getColor()->setRGB('FFFFFF');       

                        $sheet = $spreadsheet->getActiveSheet();
                        $sheet->setCellValue("A1", $translator->trans("biologist"));
                        $sheet->setCellValue("B1", $translator->trans("species"));
                        $sheet->setCellValue("C1", $translator->trans("age"));
                        $sheet->setCellValue("D1", $translator->trans("sex"));
                        $sheet->setCellValue("E1", $translator->trans("temperature"));
                        $sheet->setCellValue("F1", $translator->trans("humidity"));
                        $sheet->setCellValue("G1", $translator->trans("wind"));
                        $sheet->setCellValue("H1", $translator->trans("weather"));
                        $sheet->setCellValue("I1", $translator->trans("notes"));
                        $sheet->setCellValue("J1", $translator->trans("date"));
                        $sheet->setCellValue("K1", $translator->trans("identication_code"));
                        $sheet->setCellValue("L1", $translator->trans("neighborhood"));
                        $sheet->setCellValue("M1", $translator->trans("city"));
                        $sheet->setCellValue("N1", $translator->trans("state"));
                        $sheet->setCellValue("O1", $translator->trans("latitude"));
                        $sheet->setCellValue("P1", $translator->trans("longitude"));


                        $r=2;


                        foreach($records as $catalog) {

                            $sheet->setCellValue("A".$r, $catalog->getUser()->getFullName());
                            $sheet->setCellValue("B".$r, $catalog->getSpecies()->getScientificName());
                            $sheet->setCellValue("C".$r, $catalog->getAge());
                            $sheet->setCellValue("D".$r, $catalog->getSex());
                            $sheet->setCellValue("E".$r, $catalog->getTemperature());
                            $sheet->setCellValue("F".$r, $catalog->getHumidity());
                            $sheet->setCellValue("G".$r, $catalog->getWind());
                            $sheet->setCellValue("H".$r, $catalog->getWeather());
                            $sheet->setCellValue("I".$r, $catalog->getNotes());
                            $sheet->setCellValue("J".$r, date_format($catalog->getDate(), 'd/m/Y h:i:s'));
                            $sheet->setCellValue("K".$r, $catalog->getIdentificationCode());
                            $sheet->setCellValue("L".$r, $catalog->getNeighborhood());
                            $sheet->setCellValue("M".$r, $catalog->getCity());
                            $sheet->setCellValue("N".$r, $catalog->getState());
                            $sheet->setCellValue("O".$r, $catalog->getLatitude());
                            $sheet->setCellValue("P".$r, $catalog->getLongitude());
                            $r++;
                        }

                        $sheet->setTitle("BioBirding");

                        $writer = new Xlsx($spreadsheet);
                        $publicDirectory = $this->get("kernel")->getProjectDir() . "/public";
                        $excelFilepath =  $publicDirectory . "/reports/".$file;
                        $writer->save($excelFilepath);

                    }


                    $message = (new \Swift_Message("BioBirding"))
                        ->attach(\ Swift_Attachment::fromPath($excelFilepath)->setFilename($file))
                        ->setFrom("igor.kusmitsch@gmail.com")
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView( "emails/new_report.html.twig",
                                                array("name" => $user->getFullName())
                            ),
                            "text/html"
                    );

                    $mailer->send($message);
                    //unlink($csvPath);*/

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