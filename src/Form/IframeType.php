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
                'label' => 'Lien de partage de la vidéo',
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre lien de partage'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => VideoIframeTransformer::REGEX_URL,
                        'message' => 'Veuillez saisir un len de partage valide !'
                    ]),
                    new Regex([
                        //'pattern' => '#^<iframe.*src="((https|http)://[a-z0-9._/-]+)".*></iframe>$#i',
                      'pattern' => '#youtube|dailymotion#i',
                        'message' => 'Votre lien de partage doit contenir youtube ou dailymotion'
                    ]),
                    new NotNull([
                        'message' => 'Veuillez saisir votre lien de partage !'
                        ])
                    ],
            ]);

        $builder->get('videoIframe')
            ->addModelTransformer(new VideoIframeTransformer());
    }

}
