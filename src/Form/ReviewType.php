<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Critique',
            ])
            ->add('rating', ChoiceType::class, [
                // @link https://symfony.com/doc/current/reference/forms/types/choice.html#choices
                'choices' => [
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Bon' => 3,
                    'Peut mieux faire' => 2,
                    'A éviter' => 1
                ],
                'placeholder' => 'Votre appréciation...',
                // Si on veut masquer le label (car on a un placeholder)
                'label' => false,
            ])
            ->add('reactions', ChoiceType::class, [
                'label' => 'Ce film vous a fait...',
                'choices' => [
                    'Rire' => 'smile',
                    'Pleurer' => 'cry',
                    'Réfléchir' => 'think',
                    'Dormir' => 'sleep',
                    'Rêver' => 'dream',
                ],
                // Choix multiple : Cela va retourner un tableau
                'multiple' => true,
                // On veut un "widget HTML" par choix => checkboxes car multiple=true
                'expanded' => true,
            ])
            ->add('watchedAt', DateType::class, [
                'label' => 'Vous avez vu ce film le...',
                'input' => 'datetime_immutable',
            ])
            // On associera le film manuellement
            // ->add('movie')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
