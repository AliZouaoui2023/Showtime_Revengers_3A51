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
 
        ->add('nom_salle', TextType::class, [
           'label' => 'Nom de la salle'
       ])

       
       ->add('disponibilite', ChoiceType::class, [
           'label' => 'Disponibilité',
           'choices' => [
               'Oui' => 'oui',
               'Non' => 'non',
           ],
           'expanded' => true, 
           'multiple' => false, 
       ])

       
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

       
       ->add('nombre_de_place', IntegerType::class, [
           'label' => 'Nombre de places',
           'attr' => ['min' => 1] 
       ])

       
       ->add('statut', ChoiceType::class, [
           'label' => 'Statut de la salle',
           'choices' => [
               'Ouverte' => 'ouverte',
               'Maintenance' => 'maintenance',
               'Fermée' => 'fermee',
           ]
       ])

       
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
