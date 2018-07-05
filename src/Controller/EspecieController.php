<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Especie;
use App\Utils\Autenticar;
use Symfony\Component\Translation\TranslatorInterface;


class EspecieController extends Controller
{

    public function adicionar(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){
                $entityManager = $this->getDoctrine()->getManager();
                $especie = new Especie();
                $especie->setNomeCientifico($request->get('nome_cientifico'));
                $especie->setCaracteristicas($request->get('caracteristicas'));
                $entityManager->persist($especie);
                $entityManager->flush();
                return new JsonResponse(['status' => $translator->trans('success'), 'response' => $translator->trans('insert')]);
            }else{
                return new JsonResponse(['status' => $translator->trans('error'), 'response' => $translator->trans('insert')]); 
            }
        }catch(\TypeError | \Doctrine\DBAL\UniqueConstraintViolationException $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }


    public function selecionar(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){
                $especies = $this->getDoctrine()->getRepository(Especie::class)->findAll();
                $lista = array();
                foreach ($especies as $especie) {
                    $lista[] = array(
                                    'nome_cientifico' => $especie->getNomeCientifico(), 
                                    'caracteristicas' => $especie->getCaracteristicas()
                                    );         
                }
                return new JsonResponse(['status' => $translator->trans('success'), 'response' => $lista]);
            }
        }catch(\TypeError $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }


    public function atualizar(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){
                $entityManager = $this->getDoctrine()->getManager();
                $especie = $entityManager->getRepository(Especie::class)->find($request->get('nome_cientifico'));

                if(!$especie) {
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
                }else{
                    $especie->setNomeCientifico($request->get('novo_nome_cientifico'));
                    $especie->setCaracteristicas($request->get('caracteristicas'));
                    $entityManager->flush();
                    return new JsonResponse(['status' => $translator->trans('success'), 'response' => $translator->trans('update')]);
                }
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }


    public function remover(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){
                $entityManager = $this->getDoctrine()->getManager();
                $especie = $entityManager->getRepository(Especie::class)->find($request->get('nome_cientifico'));
                if(!$especie) {
                    throw new \Doctrine\DBAL\Exception\InvalidArgumentException($translator->trans('not_found'));
                }else{
                    $entityManager->remove($especie);
                    $entityManager->flush();
                    return new JsonResponse(['status' => $translator->trans('success'), 'response' => $translator->trans('delete')]);
                }
            }
        }catch(\TypeError | \Doctrine\DBAL\Exception\InvalidArgumentException $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }
}