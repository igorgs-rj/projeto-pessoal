<?php


namespace Desafio\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as FOS;
use FOS\RestBundle\Controller\FOSRestController;
use Desafio\Exception\InvalidIdException;


/**
 * Carteiras controller.
 */
class CarteirasController extends FOSRestController
{

    /**
     *
     * @return \Desafio\Service\CarteirasService;
     */
    public function getService()
    {
        return $this->get('Desafio\Service\CarteirasService');
    }

    /**
     *
     * @FOS\Get("/carteira/{id}", defaults={ "_format" = "json" })
    */
    public function findCarteira($id)
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

}
