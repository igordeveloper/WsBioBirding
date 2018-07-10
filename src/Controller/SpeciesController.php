<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Species;
use App\Utils\Autenticar;
use Symfony\Component\Translation\TranslatorInterface;


class SpeciesController extends Controller
{

    public function insert(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){
                $entityManager = $this->getDoctrine()->getManager();
                $species = new Species();
                $species->setScientificName($request->get('scientific_name'));
                $species->setCharacteristics($request->get('characteristics'));
                $entityManager->persist($species);
                $entityManager->flush();
                return new JsonResponse(['status' => $translator->trans('success'), 'response' => $translator->trans('insert')]);
            }else{
                return new JsonResponse(['status' => $translator->trans('error'), 'response' => $translator->trans('insert')]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException  $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }


    public function select(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){
                $speciess = $this->getDoctrine()->getRepository(Species::class)->findAll();
                $lista = array();
                foreach ($speciess as $species) {
                    $lista[] = array(
                                    'nome_cientifico' => $species->getScientificName(), 
                                    'caracteristicas' => $species->getCharacteristics()
                                    );         
                }
                return new JsonResponse(['status' => $translator->trans('success'), 'response' => $lista]);
            }
        }catch(\TypeError $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }


    public function update(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){
                $entityManager = $this->getDoctrine()->getManager();
                $species = $entityManager->getRepository(Species::class)->find($request->get('scientific_name'));

                if(!$species) {
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
                }else{
                    $species->setScientificName($request->get('new_scientific_name'));
                    $species->setCharacteristics($request->get('characteristics'));
                    $entityManager->flush();
                    return new JsonResponse(['status' => $translator->trans('success'), 'response' => $translator->trans('update')]);
                }
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\InvalidArgumentException | \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }


    public function delete(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){
                $entityManager = $this->getDoctrine()->getManager();
                $species = $entityManager->getRepository(Species::class)->find($request->get('scientific_name'));
                if(!$species) {
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
                }else{
                    $entityManager->remove($species);
                    $entityManager->flush();
                    return new JsonResponse(['status' => $translator->trans('success'), 'response' => $translator->trans('delete')]);
                }
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\InvalidArgumentException | \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }
}