<?php

namespace Desafio\Entity;

/**
 * Transferencia
 */
class Transferencia
{

    /**
     * @var string
     */
    private $transacao;

    /**
     * @var string
     */
    private $origem;

    /**
     * @var string
     */
    private $destino;

    /**
     * @var float
     */
    private $valor;

    /**
     * @var \DateTime
     */
    private $data;


    /**
     * Set transacao
     *
     * @param string $transacao
     *
     * @return Transferencia
     */
    public function setTransacao($transacao)
    {
        $this->transacao = $transacao;

        return $this;
    }

    /**
     * Get transacao
     *
     * @return string
     */
    public function getTransacao()
    {
        return $this->transacao;
    }

    /**
     * Set origem
     *
     * @param string $origem
     *
     * @return Transferencia
     */
    public function setOrigem($origem)
    {
        $this->origem = $origem;

        return $this;
    }

    /**
     * Get origem
     *
     * @return string
     */
    public function getOrigem()
    {
        return $this->origem;
    }

    /**
     * Set destino
     *
     * @param string $destino
     *
     * @return Transferencia
     */
    public function setDestino($destino)
    {
        $this->destino = $destino;

        return $this;
    }

    /**
     * Get destino
     *
     * @return string
     */
    public function getDestino()
    {
        return $this->destino;
    }

    /**
     * Set valor
     *
     * @param float $valor
     *
     * @return Transferencia
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set data
     *
     * @param \DateTime $data
     *
     * @return Transferencia
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return \DateTime
     */
    public function getData()
    {
        return $this->data;
    }

}
