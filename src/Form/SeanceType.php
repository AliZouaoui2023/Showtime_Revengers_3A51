<?php

namespace App\Form;

use App\Entity\Cour;
use App\Entity\Seance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;


class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateSeance', null, [
                'widget' => 'single_text'
            ])
            ->add('duree', TimeType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime',
                'required' => true,
                'html5' => false, // Si vous ne voulez pas que le champ soit un input de type time natif
                'attr' => [
                    'placeholder' => 'HH:MM:SS',
                    'class' => 'timepicker' // Ajoutez une classe pour le style ou le JavaScript si nécessaire
                ]
            ])
            ->add('objectifs', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'style' => 'min-height: 150px; resize: vertical;',
                    'placeholder' => 'Définissez les objectifs de cette séance...',
                    'id' => 'objectifs-textarea'
                ]
            ])
            ->add('cour', EntityType::class, [
                'class' => Cour::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}