<?php
namespace Desafio\Form\Validator;
use Symfony\Component\Validator\Constraint;

class OrigemTransferencia extends Constraint
{
    public $message = 'Apenas um cliente pode realizar transferências para outras contas.';
}