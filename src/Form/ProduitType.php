<?php
// src/Form/ProduitType.php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder' => 'Ex: Smartphone X10',
                    'maxlength' => 100,
                    'class' => 'form-control',
                ],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'TND',
                'attr' => [
                    'placeholder' => 'Ex: 99.99',
                    'class' => 'form-control',
                ],
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Électronique' => 'électronique',
                    'Vêtements' => 'vêtements',
                    'Accessoires' => 'accessoires',
                    'Alimentaire' => 'alimentaire',
                    'Maison' => 'maison',
                    'Autres' => 'autres',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez votre produit en détail...',
                    'rows' => 5,
                    'class' => 'form-control',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}