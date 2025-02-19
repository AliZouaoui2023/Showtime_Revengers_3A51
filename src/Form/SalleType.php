<?php

namespace App\Form;

use App\Entity\Salle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;




class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // Champ pour le nom de la salle
        ->add('nom_salle', TextType::class, [
           'label' => 'Nom de la salle'
       ])

       // Champ pour la disponibilité (oui / non)
       ->add('disponibilite', ChoiceType::class, [
           'label' => 'Disponibilité',
           'choices' => [
               'Oui' => 'oui',
               'Non' => 'non',
           ],
           'expanded' => true, // Permet d'afficher les options sous forme de boutons radio
           'multiple' => false, // Permet de choisir qu'une seule option
       ])

       // Champ pour le type de la salle (standard, VIP, etc.)
       ->add('type_salle', ChoiceType::class, [
           'label' => 'Type de salle',
           'choices' => [
               'Standard' => 'standard',
               'VIP' => 'VIP',
               '3D' => '3D',
               'IMAX' => 'IMAX',
               'Dolby' => 'Dolby',
               'Privée' => 'privee',
           ]
       ])

       // Champ pour le nombre de places
       ->add('nombre_de_place', IntegerType::class, [
           'label' => 'Nombre de places',
           'attr' => ['min' => 1] // Ajouter une restriction pour que le nombre soit >= 1
       ])

       // Champ pour le statut (ouverte, maintenance, fermée)
       ->add('statut', ChoiceType::class, [
           'label' => 'Statut de la salle',
           'choices' => [
               'Ouverte' => 'ouverte',
               'Maintenance' => 'maintenance',
               'Fermée' => 'fermee',
           ]
       ])

       // Champ pour l'emplacement (étage 1, 2, 3)
       ->add('emplacement', ChoiceType::class, [
           'label' => 'Emplacement',
           'choices' => [
               'Étage 1' => 'etage1',
               'Étage 2' => 'etage2',
               'Étage 3' => 'etage3',
           ]
       ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salle::class,
        ]);
    }
}
