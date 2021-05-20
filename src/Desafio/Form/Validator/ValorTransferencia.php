<?php
namespace Desafio\Form\Validator;

use Symfony\Component\Validator\Constraint;


class ValorTransferencia extends Constraint
{
    public $message = 'Saldo insuficiente para a transferência.';
}
