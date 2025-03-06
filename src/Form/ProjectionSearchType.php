<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\DateType; // For date field
use Symfony\Component\Form\Extension\Core\Type\NumberType; // For price field
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectionSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', SearchType::class, [
                'label' => 'Rechercher une projection',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entrez le nom du film ou d’autres critères...',
                    'class' => 'form-control'
                ],
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entrez un prix',
                    'class' => 'form-control'
                ],
            ])
            ->add('date_projection', DateType::class, [
                'label' => 'Date de projection',
                'required' => false,
                'widget' => 'single_text', // Displays the date picker
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET', // Use GET method for search
            'csrf_protection' => false, // Disable CSRF protection for search
        ]);
    }

    public function getBlockPrefix(): string
    {
        return ''; // Avoid prefix in form fields
    }
}
