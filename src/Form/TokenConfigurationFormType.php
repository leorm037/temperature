<?php

namespace App\Form;

use App\Entity\Configuration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TokenConfigurationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('paramValue', PasswordType::class, [
                    'label' => 'Token',
                    'always_empty' => false,
                    'required' => true,
                    'help' => 'help.token.no.show',
                    'attr' => [
                        'autofocus' => true,
                        'autocomplete' => 'off',
                    ]
                    
                ])
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
