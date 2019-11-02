<?php

namespace App\Form;

use App\Form\DataTransformer\VideoIframeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class IframeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('videoIframe', TextType::class,  [
                'label' => 'URL de la vidéo',
                'attr' => [
                    'placeholder' => 'Veuillez saisir l\'url de la vidéo'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '#^(http|https)://[a-z0-9._/-]+$#i',
//                      'pattern' => '#^<iframe.*src="((https|http)://[a-z0-9._/-]+)".*></iframe>$#i',
                        'message' => 'Veuillez saisir une url valide !'
                    ]),
                    new NotNull([
                        'message' => 'Veuillez saisir l\'url de la vidéo !'
                        ])
                    ],
            ]);

//        $builder->get('videoIframe')
//            ->addModelTransformer(new VideoIframeTransformer());
    }

}
