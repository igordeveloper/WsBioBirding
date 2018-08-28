<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Helper\AutenticateHelper;
use App\Entity\PopularName;
use App\Entity\Species;
use Symfony\Component\Translation\TranslatorInterface;


class PopularNameController extends Controller
{

    public function insert(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){
                $entityManager = $this->getDoctrine()->getManager();
                $species = $entityManager->getRepository(Species::class)->find($request->get('species'));

                $popularName = new PopularName();
                $popularName->setSpecies($species);
                $popularName->setName($request->get('name'));

                $entityManager->persist($species);
                $entityManager->persist($popularName);

                $entityManager->flush();
                return new JsonResponse(['authorized' => true, 'response' => $translator->trans('insert')]);
            }else{
                return new JsonResponse(['authorized' => false, 'response' => $translator->trans('not_authorized')]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException |  \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function selectAll(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){
                $popularName = $this->getDoctrine()->getRepository(PopularName::class)->findBy(['species' => $request->get('species')]);


                $lista = array();
                foreach ($popularName as $name) {
                    $lista[] = array(
                                    'species' => $name->getSpecies()->getScientificName(), 
                                    'name' => $name->getName()
                                    );         
                }
                return new JsonResponse(['authorized' => true , 'response' => $lista]);
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
            if($autenticate->verify($request->headers->get('authorizationCode'))){
                $popularName = $this->getDoctrine()->getRepository(PopularName::class)->find(['species' => $request->get('species'), 'name' => $request->get('name')]);


                if($popularName){
                    $lista = array(
                            'species' => $popularName->getSpecies()->getScientificName(), 
                            'name' => $popularName->getName(),
                            );  
                }else{
                    $lista = NULL;
                }

                return new JsonResponse(['status' => $translator->trans('success'), 'response' => $lista]);
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
            if($autenticate->verify($request->headers->get('authorizationCode'))){
                $entityManager = $this->getDoctrine()->getManager();
                $popularName = $entityManager->getRepository(PopularName::class)
                                ->findOneBy([
                                'species' => $request->get('species'),
                                'name' => $request->get('name')
                                ]);

                if(!$popularName) {
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
                }else{
                    $popularName->setName($request->get('newName'));
                    $entityManager->flush();
                    return new JsonResponse(['authorized' => true, 'response' => $translator->trans('update')]);
                }
            }else{
                return new JsonResponse(['authorized' => false, 'response' => $translator->trans('not_authorized')]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\InvalidArgumentException | \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function delete(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('authorizationCode'))){
                $entityManager = $this->getDoctrine()->getManager();
                $popularName = $entityManager->getRepository(PopularName::class)
                                ->findOneBy([
                                'species' => $request->get('species'),
                                'name' => $request->get('name')
                                ]);

                if(!$popularName) {
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
                }else{
                    $entityManager->remove($popularName);
                    $entityManager->flush();
                    return new JsonResponse(['authorized' => true, 'response' => $translator->trans('delete')]);
                }
            }else{
                return new JsonResponse(['authorized' => false, 'response' => $translator->trans('not_authorized')]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\InvalidArgumentException | \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }

}