<?php


namespace Desafio\Repository;

use Nasajon\MDABundle\Repository\AbstractRepository;

/**
 * CarteirasRepository
 */
class CarteirasRepository extends AbstractRepository
{


    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        parent::__construct($connection);

        $this->setLinks([
            [
                'field' => 'usuario',
                'entity' => 'Desafio\Entity\Usuario',
                'alias' => 't1_',
                'identifier' => 'usuario'
            ]
        ]);
    }

    private function findQuery(array $whereFields)
    {
        $sql = "SELECT
            t0_.carteira as \"carteira\" ,
            t0_.saldo as \"saldo\" ,
            t1_.usuario as \"t1_usuario\" ,
            t1_.nome as \"t1_nome\" ,
            t1_.email as \"t1_email\" ,
            t1_.senta as \"t1_senta\" ,
            t1_.cpfcnpj as \"t1_cpfcnpj\" ,
            t1_.tipousuario as \"t1_tipousuario\" ,
            FROM desafio.carteiras t0_
            INNER JOIN desafio.usuarios t1_ ON t1_.usuario = t0_.usuario
            WHERE t0_.carteira = :id ";
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
