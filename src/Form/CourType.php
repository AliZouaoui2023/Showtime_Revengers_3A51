<?php

namespace App\Form;

use App\Entity\Cour;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('typeCour', ChoiceType::class, [
            'choices' => [
                'Cinéma' => 'cinema',
                'Théâtre' => 'theatre',
            ],
            'placeholder' => 'Choisir un type',
            'required' => true,
           
            'label' => 'Type de cour'
        ])
        ->add('cout', NumberType::class, [
            'label' => 'Coût',
            'attr' => [
                'placeholder' => 'Ex: 150.000',
                'step' => '0.001', 
                'min' => '0.001', 
                'class' => 'currency-input'
            ],
        ])
            
        ->add('dateDebut', DateTimeType::class, [
            'widget' => 'single_text',  
            'html5' => true,         
            'attr' => ['class' => 'form-control']
        ])
        ->add('dateFin', DateTimeType::class, [
            'widget' => 'single_text',
            'html5' => true,
            'attr' => ['class' => 'form-control']
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cour::class,
        ]);
    }
}