<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('short_description')
            ->add('description')
            ->add('difficulty', ChoiceType::class, [
                'choices' => $this->getDifficulties()
            ])
            ->add('visible');
    }

    /**
     * Return array of difficulties ready to be used with ChoiceType::class
     * @return array
     */
    public function getDifficulties(): array
    {
        $result = array();

        foreach (Trick::DIFFICULTIES as $key => $value) {
            $result[$value] = $key;
        }

        return $result;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'translation_domain' => 'forms'
        ]);
    }
}
