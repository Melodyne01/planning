<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreateActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'uk-input', 'placeholder' => 'Titre'],
            ])
            ->add('description', TextType::class, [
                'attr' => ['class' => 'uk-input', 'placeholder' => 'Description'],
            ])
            ->add('startedAt', DateType::class,[
                'attr' => ['class' => 'uk-input'],
                'format' => 'yyyy : MM : dd',
                'data' => new \DateTime('Europe/Paris')
            ])
            ->add('endedAt', DateType::class, [
                'attr' => ['class' => 'uk-input'],
                'format' => 'yyyy : MM : dd',
                'data' => new \DateTime('Europe/Paris')
            ])
            ->add('category', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'name'
                    
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
