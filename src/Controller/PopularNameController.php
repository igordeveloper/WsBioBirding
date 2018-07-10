<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Utils\Autenticar;
use App\Entity\PopularName;
use App\Entity\Species;
use Symfony\Component\Translation\TranslatorInterface;


class PopularNameController extends Controller
{

    public function insert(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){

                $entityManager = $this->getDoctrine()->getManager();
                $species = $entityManager->getRepository(Species::class)->find($request->get('scientific_name'));

                $popularName = new PopularName();
                $popularName->setScientificName($species);
                $popularName->setName($request->get('popular_name'));

                $entityManager->persist($species);
                $entityManager->persist($popularName);

                $entityManager->flush();
                return new JsonResponse(['status' => $translator->trans('success'), 'response' => $translator->trans('insert')]);
            }else{
                return new JsonResponse(['status' => $translator->trans('error'), 'response' => $translator->trans('insert')]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException |  \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }


}