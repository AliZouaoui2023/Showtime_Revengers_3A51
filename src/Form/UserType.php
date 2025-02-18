<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('Nom')
        ->add('date_de_naissance', DateType::class, [
            'widget' => 'single_text',  // Render as a single text input (YYYY-MM-DD)
            'format' => 'yyyy-MM-dd',   // Date format
            'attr' => [
                'class' => 'js-datepicker', // Optional: To add a JS class for datepickers if needed
            ]
        ])
        ->add('email')
        ->add('role', ChoiceType::class, [
            'choices' => [
                'Admin' => 'Admin',
                'Coach' => 'Coach',
                'Sponsor' => 'Sponsor',
                'Client' => 'Client',
            ],
            'expanded' => false,
            'multiple' => false,
        ])
        ->add('mot_de_passe')
        ->add('photo', FileType::class, [
            'label' => 'Profile Picture (JPEG, PNG)',
            'required' => false,
            'mapped' => false, // Important: This tells Symfony that this field doesn't map directly to the entity
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\Image([
                    'mimeTypes' => ['image/jpeg', 'image/png'],
                    'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG).',
                ]),
            ],
        ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
