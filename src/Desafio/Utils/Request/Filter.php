<?php

namespace Desafio\Utils\Request;

class Filter {
    private $key;
    private $offset;
    private $order;
    private $filterExpression = [];

    public function getKey() {
        return $this->key;
    }

    public function getOffset() {
        return $this->offset;
    }


    public function setKey($key) {
        $this->key = $key;
        return $this;
    }

    public function setOffset($offset) {
        $this->offset = $offset;
        return $this;
    }

    public function getOrder() {
        return $this->order;
    }

    public function setOrder($order) {
        $this->order = $order;
        return $this;
    }
    
    public function getFilterExpression() {
        return $this->filterExpression;
    }

    public function setFilterExpression($filterExpression) {
        $this->filterExpression = $filterExpression;
        return $this;
    }
    
    public function addToFilterExpression(FilterExpression $filterExpression) {
        $this->filterExpression[] = $filterExpression;
        return $this;
    }

}
