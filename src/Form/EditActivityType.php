<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Activity;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class EditActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class, [
            'attr' => ['class' => 'uk-input', 'placeholder' => 'Titre'],
        ])
        ->add('description', TextType::class, [
            'attr' => ['class' => 'uk-input', 'placeholder' => 'Description'],
            'required' => false
        ])
        ->add('startedAt', DateTimeType::class,[
            'attr' => ['class' => 'uk-input'],
        ])
        ->add('endedAt', DateTimeType::class, [
            'attr' => ['class' => 'uk-input'],
        ])
        ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'uk-input'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
