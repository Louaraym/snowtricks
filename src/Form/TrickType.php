<?php

namespace App\Form;

use App\Entity\TricksGroup;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('tricksGroup', EntityType::class, [
                'class' => TricksGroup::class,
                'choice_label' => 'title'
            ])
            ->add('description')
           /* ->add('images', CollectionType::class, [
                'entry_type'   => ImageType::class,
                'entry_options' => ['label' => true],
                'prototype' => true,
                'allow_add'    => true,
                'allow_delete' => true
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
