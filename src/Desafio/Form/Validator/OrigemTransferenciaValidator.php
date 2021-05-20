<?php
namespace Desafio\Form\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Desafio\Service\CarteirasService;
use  Desafio\Enum\TipoUsuario;
class OrigemTransferenciaValidator extends ConstraintValidator
{
    protected $carteiraService;

    public function __construct(CarteirasService $carteiraService) {
        $this->carteiraService = $carteiraService;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof OrigemTransferencia) {
            throw new UnexpectedTypeException($constraint, OrigemTransferencia::class);
        }
        if (empty($value)) {
            return;
        }
        $carteira = $this->carteiraService->find($value);
        if($carteira['usuario']['tipousuario'] == TipoUsuario::LOJISTA) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ string }}', $value)->addViolation();
        }

    }
}
