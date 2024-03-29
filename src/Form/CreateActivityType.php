<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreateActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Crée un objet DateTime représentant la date actuelle
        $start = new \DateTime('Europe/Paris');

        // Avance la date d'une heure
        $start->modify('+1 hour');

        $start->setTime($start->format('H'), 0);

        $end = new \DateTime('Europe/Paris');

        // Avance la date d'une heure
        $end->modify('+2 hour');

        $end->setTime($end->format('H'), 0);
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
                'data' => $start
            ])
            ->add('endedAt', DateTimeType::class, [
                'attr' => ['class' => 'uk-input'],
                'data' => $end 
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
