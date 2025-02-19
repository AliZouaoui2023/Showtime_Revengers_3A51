<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montantpaye', MoneyType::class, [
                'label' => 'Montant payé',
                'currency' => 'TND',
                'attr' => [
                    'placeholder' => 'Ex: 100.00',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le montant payé est obligatoire.']),
                    new Assert\Positive(['message' => 'Le montant payé doit être un nombre positif.']),
                ],
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'État de la commande',
                'choices' => [
                    'En attente' => 'en_attente',
                    'Confirmée' => 'confirmée',
                    'Expédiée' => 'expédiée',
                    'Livrée' => 'livrée',
                ],
                'expanded' => true, // Affiche des boutons radio
                'multiple' => false,
                'attr' => [
                    'class' => 'form-check',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'état de la commande est obligatoire.']),
                ],
            ])
            ->add('produits', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nom', // Affiche le nom du produit dans les choix
                'multiple' => true, // Permet de sélectionner plusieurs produits
                'expanded' => true,  // Affiche sous forme de cases à cocher
                'label' => 'Produits',
                'by_reference' => false, // Important si la relation entre Commande et Produit est bidirectionnelle
                'attr' => [
                    'class' => 'form-check',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez sélectionner au moins un produit.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
