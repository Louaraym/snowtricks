<?php

namespace App\Form;

use App\Form\DataTransformer\VideoIframeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

class IframeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('videoIframe', TextType::class,  [
//              'constraints' => new Regex('/^<iframe/i'),
                'constraints' => new Regex('/^(http|https):/'),
                'label' => 'URL de la vidéo',
                'attr' => [
                    'placeholder' => 'Veuillez saisir l\'url de la vidéo'
                ]
            ]);

//        $builder->get('videoIframe')
//            ->addModelTransformer(new VideoIframeTransformer());
    }

}
