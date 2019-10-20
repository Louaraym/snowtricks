<?php

namespace App\Form;

use App\Entity\Member;
use App\Entity\TricksGroup;
use App\Entity\Trick;
use App\Repository\MemberRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('description', TextareaType::class)
            ->add('author', EntityType::class, [
                'class' => Member::class,
                'choice_label' => 'membername',
                'required' => true,
                'disabled' => false,
                'query_builder' => function ( MemberRepository $mr) {
                    $member = new Member();
                    return $mr->findOneByEmail($member->getEmail());
                },
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}