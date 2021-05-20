<?php

namespace Nasajon\MDABundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Connection;
use LogicException;
use Desafio\Utils\Request\Filter;

abstract class AbstractRepository {

    /**
     *
     * @var Connection 
     */
    private $_connection;

    /**
     *
     * @var array 
     */
    private $offsets = [];

    /**
     *
     * @var array 
     */
    private $filters = [];

    /**
     *
     * @var array 
     */
    private $links = [];
    
    /**
     *
     * @var array 
     */
    private $filterFields = [];
    
    /**
     *
     * @var array 
     */
    private $orders = [];
    
    /**
     *
     * @var array 
     */
    private $fields = [];

    public function __construct(Connection $connection) {
        $this->_connection = $connection;
    }

    public function validateOffset(Filter $filter = null) {
        if (is_null($filter)) {
            return;
        }
        if(is_array($filter->getOffset()) && count($filter->getOffset()) != 2 && (is_array($filter->getOrder()) || count($this->offsets) > 1)){
            throw new LogicException("O offset deve conter o valor da ordenação principal e do id.");
        } elseif (is_array($filter->getOffset())) {
            $offsetKeys = array_keys($filter->getOffset());
            foreach( $offsetKeys as $key ){
                if (!array_key_exists($key,$this->offsets) && !array_key_exists($key,$this->orders)) {
                    throw new LogicException("O campo {$key} não é um campo de offset.");
                }
            } 
        }
        if (is_array($filter->getOrder())) {
            $orderKeys = array_keys($filter->getOrder());
            foreach( $orderKeys as $key ){
                if (!array_key_exists($key,$this->orders)) {
                    throw new LogicException("O campo {$key} não é um campo de ordenação.");
                }
            } 
        }
    }

    public function processOffset(Filter $filter = null) {
        $where = [];
        $binds = [];
        $queryBuilder = $this->getConnection()->createQueryBuilder();
        if (!is_null($filter) && !empty($filter->getOffset()) && empty($filter->getOrder())) {            
            if (!is_array($filter->getOffset())) {
                $offsetField = array_values($this->offsets)[0];
                if ($offsetField['direction'] == Criteria::ASC) {
                    $where[] = $queryBuilder->expr()->gt('t0_.' . $offsetField['column'], '?');
                } else {
                    $where[] = $queryBuilder->expr()->lt('t0_.' . $offsetField['column'], '?');
                }
                $binds[] = $filter->getOffset();
            } elseif( count($this->offsets) > 1 ) {
                $offsetField = array_values($this->offsets)[0];
                $offsetKey = array_keys($this->offsets)[0];
                if ($offsetField['direction'] == Criteria::ASC) {
                    $where[] = $queryBuilder->expr()->gte('t0_.' . $offsetField['column'], '?');
                } else {
                    $where[] = $queryBuilder->expr()->lte('t0_.' . $offsetField['column'], '?');
                }
                $binds[] = $filter->getOffset()[$offsetKey];
                
                $offsetId = array_values($this->offsets)[count($this->offsets)-1];
                $offsetIdKey = array_keys($this->offsets)[count($this->offsets)-1];
                $where[] = $queryBuilder->expr()->orX($queryBuilder->expr()->neq('t0_.' . $offsetField['column'], '?'),$queryBuilder->expr()->gt('t0_.' . $offsetId['column'], '?'));
                $binds[] = $filter->getOffset()[$offsetKey];
                $binds[] = $filter->getOffset()[$offsetIdKey];
            } else {
                $offsetField = array_values($this->offsets)[0];
                $offsetKey = array_keys($this->offsets)[0];
                if ($offsetField['direction'] == Criteria::ASC) {
                    $where[] = $queryBuilder->expr()->gt('t0_.' . $offsetField['column'], '?');
                } else {
                    $where[] = $queryBuilder->expr()->lt('t0_.' . $offsetField['column'], '?');
                }
                $binds[] = $filter->getOffset()[$offsetKey];
            }
        } elseif(!is_null($filter) && !empty($filter->getOffset()) && !empty($filter->getOrder())){
            $offsetDirection = array_values($filter->getOrder())[0];
            $offsetKey = array_keys($filter->getOrder())[0];
            if (strcasecmp($offsetDirection, Criteria::ASC) == 0) {
                $where[] = $queryBuilder->expr()->gte('t0_.' . $this->orders[$offsetKey], '?');
            } else {
                $where[] = $queryBuilder->expr()->lte('t0_.' . $this->orders[$offsetKey], '?');
            }
            $binds[] = $filter->getOffset()[$offsetKey];

            $offsetId = array_values($this->offsets)[count($this->offsets)-1];
            $offsetIdKey = array_keys($this->offsets)[count($this->offsets)-1];
            $where[] = $queryBuilder->expr()->orX($queryBuilder->expr()->neq('t0_.' . $this->orders[$offsetKey], '?'),$queryBuilder->expr()->gt('t0_.' . $offsetId['column'], '?'));
            $binds[] = $filter->getOffset()[$offsetKey];
            $binds[] = $filter->getOffset()[$offsetIdKey];
        }

        return [$where, $binds];
    }

    public function processFilter($filter) {
        $filters = [];
        $binds = [];
        if (!is_null($filter) && !empty($filter->getKey())) {
            $queryBuilder = $this->getConnection()->createQueryBuilder();
            foreach ($this->filters as $fi) {
                $filters[] = $queryBuilder->expr()->comparison($fi . '::text', "ILIKE", "?");
                $binds[] = "%" . $filter->getKey() . "%";
            }
        }
        return [$filters, $binds];
    }
    
    public function processFilterExpression($filter) {
        $filters = [];
        $binds = [];

        if (!is_null($filter) && !empty($filter->getFilterExpression())) {
            $queryBuilder = $this->getConnection()->createQueryBuilder();
            foreach ($filter->getFilterExpression() as $filterExpression) {
                if( $filterExpression->getCondition() == 'isNull' || $filterExpression->getCondition() == 'isNotNull'){
                    $filters[$filterExpression->getField()][$filterExpression->getCondition()][] = $queryBuilder->expr()->{$filterExpression->getCondition()}($this->filterFields[$filterExpression->getField()]);
                } else if( $filterExpression->getCondition() == 'eq' && $filterExpression->getValue() == null){
                    $filters[$filterExpression->getField()]['isNull'][] = $queryBuilder->expr()->isNull($this->filterFields[$filterExpression->getField()]);
                } else {
                    $filters[$filterExpression->getField()][$filterExpression->getCondition()][] = $queryBuilder->expr()->{$filterExpression->getCondition()}($this->filterFields[$filterExpression->getField()], "?");
                    $binds[] = $filterExpression->getValue();
                }
            }

            $filters = array_map(function($filtro) use($queryBuilder) {
                $filter = array_reduce($filtro, function($and, $expressions) use($queryBuilder) {
                    $ors = array_reduce($expressions, function($or, $expression) use($queryBuilder) {
                        return $queryBuilder->expr()->orX($or, $expression);
                    });
                    if( strpos($ors->__toString(), 'IS NULL') !== FALSE ){
                        return $queryBuilder->expr()->orX($and, $ors);
                    }
                    return $queryBuilder->expr()->andX($and, $ors);
                });
                return $filter;
            }, $filters);
        }
        return [$filters, $binds];
    }
    
    /**
     * Inicia uma transação
     */
    public function begin(){
        $this->getConnection()->beginTransaction();   
        $this->getConnection()->setAutoCommit(false);
    }
    
    /**
     * Finaliza a transação aberta
     */
    public function commit(){
        $this->getConnection()->commit();     
    }

    /**
     * Cancela a transação aberta
     */
    public function rollback(){
        $this->getConnection()->rollBack();
    }
    
    /**
     * 
     * @return Connection $connection
     */
    public function getConnection() {
        return $this->_connection;
    }

    /**
     * 
     * @return array
     */
    public function getOffsets() {
        return $this->offsets;
    }

    /**
     * 
     * @return array
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * 
     * @return array
     */
    public function getLinks() {
        return $this->links;
    }
    
    /**
     * 
     * @return array
     */
    public function getOrders() {
        return $this->orders;
    }
    
    /**
     * 
     * @return AbstractRepository
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * 
     * @param array $offsets
     * @return AbstractRepository
     */
    public function setOffsets($offsets) {
        $this->offsets = $offsets;
        return $this;
    }

    /**
     * 
     * @param array $filters
     * @return AbstractRepository
     */
    public function setFilters($filters) {
        $this->filters = $filters;
        return $this;
    }

    /**
     * 
     * @param array $links
     * @return AbstractRepository
     */
    public function setLinks($links) {
        $this->links = $links;
        return $this;
    }
    
    /**
     * 
     * @param array $filterFields
     * @return AbstractRepository
     */
    public function setFilterFields($filterFields) {
        $this->filterFields = $filterFields;
        return $this;
    }
    
    /**
     * 
     * @param array $orders
     * @return AbstractRepository
     */
    public function setOrders($orders) {
        $this->orders = $orders;
        return $this;
    }
    
    /**
     * 
     * @param array $fields
     * @return AbstractRepository
     */
    public function setFields($fields) {
        $this->fields = $fields;
        return $this;
    }

}
