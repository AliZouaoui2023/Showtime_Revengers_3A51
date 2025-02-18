<?php

namespace App\Form;

use App\Entity\Cour;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

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
                'class' => 'currency-input'
            ],
            'html5' => true,
            'scale' => 2 // Pour les décimales
        ])
            
            ->add('dateDebut', null, [
                'widget' => 'single_text'
            ])
            ->add('dateFin', null, [
                'widget' => 'single_text'
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
