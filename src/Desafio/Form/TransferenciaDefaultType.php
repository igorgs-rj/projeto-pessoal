<?php
/*
    Código gerado automaticamente pelo Transformer do MDA 
*/


namespace Desafio\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Desafio\Utils\EntityHydrator;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Desafio\Form\Validator\OrigemTransferencia;
use Desafio\Form\Validator\ValorTransferencia;

class TransferenciaDefaultType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('origem', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
            'label' => 'Origem',
            'required' => true,
            'error_bubbling' => true,
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\NotBlank([
                    'message' => 'O campo nome não pode ser vazio.',
                ]),
                new OrigemTransferencia([
                    'message' => 'Apenas um cliente pode realizar transferências para outras contas.',
                ]),
            ],

        ));

        $builder->add('destino', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
            'label' => 'Destino',
            'required' => true,
            'error_bubbling' => true,
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\NotBlank([
                    'message' => 'O campo nome não pode ser vazio.',
                ]),
            ],

        ));

        $builder->add('valor', \Symfony\Component\Form\Extension\Core\Type\NumberType::class, array(
            'label' => 'Valor',
            'required' => true,
            'error_bubbling' => true,
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\Type([
                    'message' => 'Valor informado não é um número.',
                    'type' => 'numeric',
                ]),
                new \Symfony\Component\Validator\Constraints\NotBlank([
                    'message' => 'Informe o conteúdo.',
                ]),
                new ValorTransferencia([
                    'message' => 'Saldo insuficiente para a transferência.',
                ]),
            ],

        ));



        $builder->addEventListener(\Symfony\Component\Form\FormEvents::PRE_SUBMIT, function (\Symfony\Component\Form\FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            if (!empty($form->getConfig()->getDataClass())) {
                $entity = EntityHydrator::hydrate($form->getConfig()->getDataClass(),  $data);
            }
        });
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Desafio\Entity\Transferencia',
            'attr' => ['novalidate' => 'novalidate'],
            'csrf_protection'   => false,
            'allow_extra_fields' => true,
        ));
    }

}
