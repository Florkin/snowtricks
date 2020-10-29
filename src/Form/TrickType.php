<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('difficulty', ChoiceType::class, [
                'choices' => $this->getDifficulties()
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'group_by' => function (Category $category) {
                    if (!is_null($category->getParentCategory())) {
                        return $category->getParentCategory()->getTitle();
                    }
                    return null;
                }
            ])
            ->add('visible', CheckboxType::class, [
                'attr' =>  [
                    'class' => 'switch'
                ]
            ])
            ->add('pictures', CollectionType::class, [
                'label' => false,
                'entry_type' => PictureType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'label' => false,
                'delete_empty' => true,
                'by_reference' => false,
            ]);
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
