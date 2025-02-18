<?php

namespace App\Form;

use App\Entity\Cour;
use App\Entity\Seance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Assurez-vous que c'est bien ce namespace
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateSeance', null, [
                'widget' => 'single_text'
            ])
            ->add('duree')
            ->add('objectifs')
            ->add('cour', EntityType::class, [
                'class' => Cour::class,
                'choice_label' => 'id', // Vous pouvez le changer pour un autre attribut comme 'typeCour'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}
