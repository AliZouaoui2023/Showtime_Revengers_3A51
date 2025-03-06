<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Equipement;
use App\Entity\Salle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom de l\'équipement',
            'attr' => ['class' => 'form-control']
        ])
        ->add('type', ChoiceType::class, [
            'choices' => [
                'Équipements des Salles Premium' => 'salles_premium',
                'Équipements de billetterie' => 'billetterie',
                'Équipements techniques' => 'techniques',
                'Équipements du snack-bar' => 'snack_bar',
                'Équipements de projection' => 'projection',
                'Équipements audio' => 'audio',
                'Équipements pour spectateurs' => 'spectateurs',
            ],
            'placeholder' => 'Sélectionnez un type',
            'required' => true,
            'expanded' => false,
            'multiple' => false,
        ])
        
        ->add('etat', ChoiceType::class, [
            'choices' => [
                'En service' => 'En service',
                'Hors service' => 'Hors service',
                'Endommagé' => 'Endommagé',
            ],
            'placeholder' => 'Sélectionnez un état',
            'expanded' => false,
            'multiple' => false,
        ])
        ->add('salle', EntityType::class, [  // Déplacer le champ salle ici
            'class' => Salle::class,
            'choice_label' => 'nom_salle',
            'placeholder' => 'Sélectionnez une salle',
            'required' => true,
            'attr' => ['class' => 'form-control'],
        ]);
    }    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class, // Lier le formulaire à l'entité Equipement
        ]);
    }
}
