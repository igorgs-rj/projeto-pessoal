<?php

namespace Desafio\Entity;

/**
 * Carteira
 */
class Carteira
{

    /**
     * @var string
     */
    private $carteira;

    /**
     * @var string
     */
    private $usuario;


    /**
     * @var float
     */
    private $saldo;



    /**
     * Set carteira
     *
     * @param string $carteira
     *
     * @return Transferencia
     */
    public function setCarteira($carteira)
    {
        $this->carteira = $carteira;

        return $this;
    }

    /**
     * Get carteira
     *
     * @return string
     */
    public function getCarteira()
    {
        return $this->carteira;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     *
     * @return Transferencia
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set saldo
     *
     * @param float $saldo
     *
     * @return Transferencia
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return float
     */
    public function getSaldo()
    {
        return $this->saldo;
    }


}
