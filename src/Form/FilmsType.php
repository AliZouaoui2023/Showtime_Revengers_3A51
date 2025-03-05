<?php

namespace App\Form;

use App\Entity\Films;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FilmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom_Film')
            ->add('Date_Production', null, [
                'widget' => 'single_text',
            ])
            ->add('Realisateur')
            ->add('Genre',ChoiceType::class,[
                'choices'=>[
                    'Horror'=>'Horror',
                    'Comedy'=>'Comedy',
                    'Romance'=>'Romance',
                    'Action'=>'Action',
                    'Drama'=>'Drama',
                    'Suspense'=>'Suspense',
                ],
                'placeholder'=>'choisir un genre',
                'required'=>true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Films::class,
        ]);
    }
}
