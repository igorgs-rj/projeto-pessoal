<?php

namespace Desafio\Exception;

class InvalidFilterException extends \Exception{
    
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null) {
        $message = "Filtro inválido porque " . $message;
        
        parent::__construct($message, $code, $previous);
    }
}
