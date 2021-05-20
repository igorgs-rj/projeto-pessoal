<?php

namespace Desafio\Utils\Request;
class FilterExpression {

    private $condition;
    private $value;
    private $field;

    public function __construct( $field, $condition, $value )
    {
        $this->field = $field;
        $this->condition = $condition;
        $this->value = $value;
    }
        public function getCondition(){
        return $this->condition;
    }
        public function getValue(){
        return $this->value;
    }
    
    public function getField(){
        return $this->field;
    }
}
