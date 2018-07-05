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

                try{
                    $entityManager->persist($especie);
                    $entityManager->flush();
                    $status =  $translator->trans('success');
                    $mensagem = $translator->trans('insert');

                }catch(\Doctrine\DBAL\DBALException $ex) {
                    $status =  $translator->trans('error');
                    $mensagem =$ex->getmessage();
                }
            }
        }catch(\Throwable $ex){
                    $status =  $translator->trans('error');
                    $mensagem =$ex->getmessage();
        }

        return new JsonResponse(
            [
                'status' => $status,
                'mensagem' => $mensagem
            ],
            JsonResponse::HTTP_CREATED
        );
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
        }catch(\Throwable $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }


    public function remover(Request $request, Autenticar $autenticar, TranslatorInterface $translator)
    {

        try{
            if($autenticar->token($request->headers->get('token'))){

            $entityManager = $this->getDoctrine()->getManager();
            $especie = $entityManager->getRepository(Especie::class)->find($request->get('nome_cientifico'));
            $entityManager->remove($especie);
            $entityManager->flush();

            return new JsonResponse(['status' => $translator->trans('success'), 'response' => '$lista']);

            }
        }catch(\Throwable $ex){
            return new JsonResponse(['status' => $translator->trans('error'), 'response' => $ex->getmessage()]);
        }
    }
}