<?php

namespace App\Form;

use App\Entity\EmbedVideo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("url", TextType::class, [
                "label" => false,
                "attr" => ["placeholder" => "Lien de vidéo youtube"]
            ])
            ->add("delete", ButtonType::class, [
                "attr" => ["class" => "btn-outline-danger js-delete-video"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EmbedVideo::class,
        ]);
    }
}
