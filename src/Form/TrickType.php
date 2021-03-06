<?php

namespace App\Form;

use App\Entity\TricksGroup;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

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
            ->add('description', TextareaType::class);

        $imageConstraints = [
            new Image([
                'maxSize' => '2M',
            ]),
        ];

        $builder
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => $imageConstraints,
            ])
            ->add('videos', CollectionType::class,[
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => IframeType::class,
            ])
        ;

    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class
        ]);
    }
}