<?php
namespace Desafio\Form\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Desafio\Service\CarteirasService;

class ValorTransferenciaValidator extends ConstraintValidator
{
    protected $carteiraService;

    public function __construct(CarteirasService $carteiraService) {
        $this->carteiraService = $carteiraService;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValorTransferencia) {
            throw new UnexpectedTypeException($constraint, ValorTransferencia::class);
        }

        if (empty($value)) {
            return;
        }
        $id =  $this->context->getRoot()->get('origem')->getData();
        $carteira = $this->carteiraService->find($id);
        if($carteira['saldo'] < $value){
            $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
        }


    }
}
