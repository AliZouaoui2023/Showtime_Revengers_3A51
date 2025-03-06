<?php

namespace App\Form;

use App\Entity\Demande;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbrJours')
            ->add('description')
            ->add('type')
            ->add('lienSupp')
            ->add('statut')
            ->add('dateSoumission', null, [
                'widget' => 'single_text',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('admin', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('recaptcha', EWZRecaptchaType::class, [
                'mapped' => false,
                'constraints' => [
                    new RecaptchaTrue([
                        'message' => 'Veuillez valider le captcha'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}