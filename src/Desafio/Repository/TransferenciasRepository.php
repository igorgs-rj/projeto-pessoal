<?php


namespace Desafio\Repository;

use Nasajon\MDABundle\Repository\AbstractRepository;
use Desafio\Entity\Transferencia;

/**
 * TransferenciasRepository
 */
class TransferenciasRepository extends AbstractRepository
{


    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        parent::__construct($connection);
    }


    /**
     * @param Transferencia $entity
     * @return string 
     * @throws \Exception
     */
    public function insert(Transferencia $entity)
    {
        $sql_1 = "SELECT *
            FROM desafio.transferencia(row(
                    :valor,                  
                    :origem,
                    :destino
                )::desafio.t_transferencia
        );";
        $stmt_1 = $this->getConnection()->prepare($sql_1);
        $stmt_1->bindValue("valor", $entity->getValor());
        $stmt_1->bindValue("origem", $entity->getOrigem());
        $stmt_1->bindValue("destino", $entity->getDestino());
        $resposta = $stmt_1->executeQuery();
        return $resposta;
    }


    private function findQuery(array $whereFields)
    {
        $sql = "SELECT
            t0_.transacao as \"transacao\" ,
            t0_.origem as \"origem\" ,
            t0_.destino as \"destino\" ,
            t0_.codigo as \"codigo\" ,
            t0_.valor as \"valor\" ,
            t0_.data as \"data\" 
            FROM desafio.transacoes t0_
            WHERE t0_.transacao = :id ";
        return $this->getConnection()->executeQuery($sql, $whereFields);
    }

    /**
     * @param string $id
        
     * @return array
     * @throw \Doctrine\ORM\NoResultException
     */
    public function find($id)
    {
        $data = $this->findQuery([
            'id' => $id
        ])->fetchOne();
        $data = $this->adjustQueryData($data);
        return $data;
    }

    
    public function adjustQueryData($data)
    {
        if (!$data) {
            throw new \Doctrine\ORM\NoResultException();
        }
        foreach ($this->getLinks() as $link) {
            $newArr = [];
            foreach ($data as $subKey => $value) {
                if (substr($subKey, 0, strlen($link['alias'])) === $link['alias']) {
                    $newArr[str_replace($link['alias'], "", $subKey)] = $value;
                    unset($data[$subKey]);
                }
            }
            if (is_null($newArr[$link['identifier']])) {
                $data[$link['field']] = null;
            } else {
                $data[$link['field']] = $newArr;
            }
        }
        return $data;
    }

}
