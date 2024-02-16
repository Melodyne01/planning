<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => ['class' => 'uk-input', 'placeholder' => 'Prénom'],
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'uk-input', 'placeholder' => 'Nom'],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'uk-input', 'placeholder' => 'Adresse mail'],
            ])
            ->add('password', PasswordType::class,[
                'attr' => ['class' => 'uk-input ', 'placeholder' => 'Mot de passe'],
            ])
            ->add('confirmPassword', PasswordType::class,[
                'attr' => ['class' => 'uk-input ', 'placeholder' => 'Confirmer le mot de passe'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
