<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Species;
use App\Helper\AutenticateHelper;
use Symfony\Component\Translation\TranslatorInterface;


class SpeciesController extends AbstractController
{

    public function insert(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('nickname'), $request->headers->get('password'))){
                $entityManager = $this->getDoctrine()->getManager();
                $species = new Species();
                $species->setScientificName($request->get('scientific_name'));
                $species->setCharacteristics($request->get('characteristics'));
                $entityManager->persist($species);
                $entityManager->flush();
                return new JsonResponse(['authorized' => true, 'response' => $translator->trans('insert')]);
            }else{
                return new JsonResponse(['authorized' => false]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException  $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function search(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('nickname'), $request->headers->get('password'))){
                $species = $this->getDoctrine()->getRepository(Species::class)->findByScientificName($request->get('scientific_name'));
                $lista = array();


                foreach ($species as $specie) {


                    $lista[] = array(
                                    'scientific_name' => $specie->getScientificName(), 
                                    'characteristics' => $specie->getCharacteristics()
                                    );         
                }
                return new JsonResponse(['authorized' => true , 'species' => $lista]);
            }else{
                return new JsonResponse(['authorized' => false, 'response' => $translator->trans('not_authorized')]); 
            }
        }catch(\TypeError $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function select(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('nickname'), $request->headers->get('password'))){
                $species = $this->getDoctrine()->getRepository(Species::class)->find($request->get('scientific_name'));
                
                if($species){
                    $lista = array(
                            'scientific_name' => $species->getScientificName(), 
                            'characteristics' => $species->getCharacteristics()
                            );  
                }else{
                    $lista = NULL;
                }

                return new JsonResponse(['authorized' => true , 'species' => $lista]);
            }else{
                return new JsonResponse(['authorized' => false, 'response' => $translator->trans('not_authorized')]); 
            }
        }catch(\TypeError $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function update(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('nickname'), $request->headers->get('password'))){
                $entityManager = $this->getDoctrine()->getManager();
                $species = $entityManager->getRepository(Species::class)->find($request->get('scientific_name'));

                if(!$species) {
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
                }else{
                    $species->setScientificName($request->get('new_scientific_name'));
                    $species->setCharacteristics($request->get('characteristics'));
                    $entityManager->flush();
                  return new JsonResponse(['authorized' => true, 'response' => $translator->trans('update')]);

                }
            }
        }catch(\TypeError |  \Doctrine\DBAL\Exception\UniqueConstraintViolationException | \Doctrine\DBAL\Exception\InvalidArgumentException | \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function delete(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->token($request->headers->get('token'))){
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