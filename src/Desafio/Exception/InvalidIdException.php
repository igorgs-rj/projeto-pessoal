<?php

namespace Desafio\Exception;

class InvalidIdException extends \Exception{
    
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null) {
        $message = empty($message) ? "Id em formato inválido" : $message;
        
        parent::__construct($message, $code, $previous);
    }
}
