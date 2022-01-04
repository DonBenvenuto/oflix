<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Film' => 'Film',
                    'Série' => 'Série',
                ],
                // Bontons radios
                'expanded' => true,                
            ])
            ->add('releaseDate', DateType::class, [
                // Les années depuis le premier film à + 10 ans
                'years' => range(1895, date('Y') + 10),
                // Widget HTML5
                'html5' => true, // Déjà le cas par défaut
                'format' => 'yyyy-MM-dd', // Format nécessaire pour la datepicker
                'widget' => 'single_text',
            ])
            ->add('duration', null, [
                'help' => 'Durée en minutes',
            ])
            ->add('summary')
            ->add('synopsis')
            ->add('poster', UrlType::class, [
                'help' => 'URL de l\'image',
            ])
            ->add('rating')
            ->add('genres', EntityType::class, [
                // @link https://symfony.com/doc/current/reference/forms/types/entity.html#basic-usage
                'class' => Genre::class,
                // Requête custom pour trier les genres par ordre alpha
                // https://symfony.com/doc/current/reference/forms/types/entity.html#using-a-custom-query-for-the-entities
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    /**
     * Les options du form ou "de la balise form"
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
            // Nos attributs HTML
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
