<?php

namespace App\Form;

use App\Entity\Demande;
use App\Entity\Publicite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PubliciteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotNull(['message' => 'La date de début est obligatoire.']),
                    new Assert\GreaterThan([
                        'value' => 'today',
                        'message' => 'La date de début doit être dans le futur.'
                    ])
                ]
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotNull(['message' => 'La date de fin est obligatoire.']),
                    new Assert\GreaterThan([
                        'propertyPath' => 'parent.all[dateDebut].data',
                        'message' => 'La date de fin doit être postérieure à la date de début.'
                    ])
                ]
            ])
            ->add('support', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le support est obligatoire.']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Le support doit contenir au moins 3 caractères.'
                    ])
                ]
            ])
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR',
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le montant est obligatoire.']),
                    new Assert\Positive(['message' => 'Le montant doit être un nombre positif.'])
                ]
            ])
            ->add('demande', EntityType::class, [
                'class' => Demande::class,
                'choice_label' => 'id',
                'constraints' => [
                    new Assert\NotNull(['message' => 'La demande associée est obligatoire.'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publicite::class,
        ]);
    }
}
