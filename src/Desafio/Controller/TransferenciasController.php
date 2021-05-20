<?php


namespace Desafio\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as FOS;
use FOS\RestBundle\Controller\FOSRestController;
use Desafio\Exception\RepositoryException;
use Desafio\Entity\Transferencia;
use Desafio\Form\TransferenciaDefaultType;
use Desafio\Exception\InvalidIdException;

/**
 * Transferencias controller.
 */
class TransferenciasController extends FOSRestController
{

    /**
     *
     * @return \Desafio\Service\TransferenciasService;
     */
    public function getService()
    {
        return $this->get('Desafio\Service\TransferenciasService');
    }

    /**
     *
     * @FOS\Post("/transferencia/", defaults={ "_format" = "json" })
     */
    public function createTransferencia(Request $request)
    {
        try {
            $entity = new Transferencia();
            $form = $this->createDefaultForm($entity, 'POST', 'insert');
            $form->handleRequest($request);
            if ($form->isValid()) {
                $service = $this->getService();
                $retorno = $service->insert($entity);

                return new JsonResponse($retorno, JsonResponse::HTTP_CREATED);
            } else {
                return $this->handleView($this->view($form, JsonResponse::HTTP_BAD_REQUEST));
            }
        } catch (RepositoryException $e) {
            return new JsonResponse(["message" => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     *
     * @FOS\Get("/transferencia/{id}", defaults={ "_format" = "json" })
    */
    public function findTransferencia($id)
    {
        try{    
        $entity = $this->getService()->find($id);
        $response = new JsonResponse($entity);                        
        return $response;
        }catch(\Doctrine\ORM\NoResultException $e){
            return new JsonResponse(["message" => $e->getMessage(), 'id' => $id ], JsonResponse::HTTP_NOT_FOUND);            
        }catch(InvalidIdException $e){
            return new JsonResponse(["message" => $e->getMessage(), 'id' => $id ], JsonResponse::HTTP_NOT_FOUND);            
        }
        
    }


    /**
     *  
     * @param Transferencia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createDefaultForm(Transferencia $entity, $method = "POST", $action = 'insert')
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder(NULL, TransferenciaDefaultType::class, $entity, array(
                'method' => $method,
                'action' => $action,
            ))
            ->getForm();
        return $form;
    }
}
