<?php

namespace Desafio\Service;

use Desafio\Repository\CarteirasRepository;
use Desafio\Entity\Transferencia;

/**
 * CarteirasService
 *
 */
class CarteirasService
{
    /**
     * @var CarteirasRepository
     */
    protected $repository;

    /**
     * @return CarteirasRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function __construct(CarteirasRepository $repository)
    {
        $this->repository = $repository;
    }


    public function find($id)
    {
        $data = $this->getRepository()->find($id);
        return $data;
    }
}
