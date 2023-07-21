<?php

namespace App\Form;

use App\Entity\Configuration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TokenConfigurationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('paramValue', null, [
                    'label' => 'Token',
                    'required' => true,
                    'attr' => [
                        'autofocus' => true,
                    ]
                    
                ])
//                ->add('paramName', HiddenType::class, [
//                    'data' => 'token',
//                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Configuration::class,
            'translation_domain' => 'label',
        ]);
    }
}
