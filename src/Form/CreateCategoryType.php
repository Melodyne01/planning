<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreateCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'uk-input', 'placeholder' => 'Category'],
            ])
            ->add('color', ChoiceType::class, [
                'choices' => [
                    "Bleu" => "blue",
                    "Orange" => "orange",
                    "Rouge" => "red",
                    "Vert" => "green",
                    "Gris" => "grey",
                ],
                'attr' => ['class' => 'uk-input', 'placeholder' => 'Couleur'],
            ])
            ->add('parent', ChoiceType::class, [
                'choices' => [
                    'Sport' => 'Sport',
                    'Loisirs' => 'Loisirs',
                    'Travail' => 'Travail',
                    'Famille' => 'Famille',
                    'Autre' => 'Autre',
                ],
                'expanded' => true,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
