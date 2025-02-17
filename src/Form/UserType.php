<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom')
            ->add('date_de_naissance', null, [
                'widget' => 'single_text',
            ])
            ->add('email')
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'Admin',
                    'Coach' => 'Coach',
                    'Sponsor' => 'Sponsor',
                    'Client' => 'Client',
                ],
                'expanded' => false, // This makes it a dropdown (select box)
                'multiple' => false, // This ensures only one role can be selected
            ])
            ->add('mot_de_passe')
            ->add('photo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
