<?php

namespace Desafio\Service;

use Desafio\Repository\TransferenciasRepository;
use Desafio\Entity\Transferencia;
use Desafio\Client\AuthorizationCliente;
use Desafio\Client\EmailClient;
use Exception;

/**
 * TransferenciasService
 *
 */
class TransferenciasService
{
    /**
     * @var TransferenciasRepository
     */
    protected $repository;


    /**
     * @var AuthorizationCliente
     */    
    protected $authorizationClient;

    /**
     * @var EmailClient
     */    
    protected $emailClient;

    /**
     * @return TransferenciasRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function __construct(TransferenciasRepository $repository)
    {
        $this->repository = $repository;
        $this->authorizationClient = new AuthorizationCliente();
        $this->emailClient = new EmailClient();

    }


    /**
     * @param Transferencia $entity
     * @return string
     * @throws \Exception
     */
    public function insert(Transferencia $entity)
    {
        try {
            $this->getRepository()->begin();
            // $servicoAutorizado = $this->authorizationClient->verificarAutorizadorExterno($entity);
            // if(!$servicoAutorizado){
            //     throw new Exception('Tranferencia não autorizada');
            // }
            $response = $this->getRepository()->insert($entity);
            //$this->emailClient->enviarEmailDestino($response);
            $this->getRepository()->commit();
            return $response;
        } catch (\Exception $e) {
            $this->getRepository()->rollBack();
            throw $e;
        }
    }

    public function find($id)
    {
        $data = $this->getRepository()->find($id);
        return $data;
    }


}
