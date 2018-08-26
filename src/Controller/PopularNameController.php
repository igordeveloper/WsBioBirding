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
            if($autenticate->verify($request->headers->get('nickname'), $request->headers->get('password'))){

                $entityManager = $this->getDoctrine()->getManager();
                $species = $entityManager->getRepository(Species::class)->find($request->get('scientific_name'));

                $popularName = new PopularName();
                $popularName->setScientificName($species);
                $popularName->setName($request->get('name'));

                $entityManager->persist($species);
                $entityManager->persist($popularName);

                $entityManager->flush();
                return new JsonResponse(['authorized' => true, 'response' => $translator->trans('insert')]);
            }else{
                return new JsonResponse(['authorized' => false]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\UniqueConstraintViolationException |  \Doctrine\ORM\ORMException $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function selectAll(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('nickname'), $request->headers->get('password'))){
                $popularName = $this->getDoctrine()->getRepository(PopularName::class)->findBy(['scientificName' => $request->get('name')]);


                $lista = array();
                foreach ($popularName as $name) {
                    $lista[] = array(
                                    'scientific_name' => $name->getScientificName()->getScientificName(), 
                                    'name' => $name->getName()
                                    );         
                }
                return new JsonResponse(['status' => $translator->trans('success'), 'response' => $lista]);
            }
        }catch(\TypeError $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function select(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->verify($request->headers->get('nickname'), $request->headers->get('password'))){
                $popularName = $this->getDoctrine()->getRepository(PopularName::class)->findBy(['name' => "%".$request->get('name')."%"]);


                $lista = array();
                foreach ($popularName as $name) {
                    $lista[] = array(
                                    'scientific_name' => $name->getScientificName()->getScientificName(), 
                                    'name' => $name->getName()
                                    );         
                }
                return new JsonResponse(['status' => $translator->trans('success'), 'response' => $lista]);
            }
        }catch(\TypeError $ex){
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }


    public function update(Request $request, AutenticateHelper $autenticate, TranslatorInterface $translator)
    {

        try{
            if($autenticate->token($request->headers->get('token'))){
                $entityManager = $this->getDoctrine()->getManager();
                $popularName = $entityManager->getRepository(PopularName::class)
                                ->findOneBy([
                                'scientific_name' => $request->get('scientific_name'),
                                'name' => $request->get('name')
                                ]);

                if(!$popularName) {
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
                }else{
                    $popularName->setName($request->get('new_name'));
                    $entityManager->flush();
                    return new JsonResponse(['status' => $translator->trans('success'), 'response' => $translator->trans('update')]);
                }
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\InvalidArgumentException | \Doctrine\ORM\ORMException $ex){
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
            return new JsonResponse(['exception' => $ex->getmessage()]);
        }
    }

}