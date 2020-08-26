<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
//                'query_builder' => function (CategoryRepository $categoryRepository) {
//                    return $categoryRepository->createQueryBuilder("p")
//                        ->where("p.parentCategory IS NOT NULL");
//                },
                'group_by' => function (Category $category) {
                    if (!is_null($category->getParentCategory())) {
                        return $category->getParentCategory()->getTitle();
                    }
                    return null;
                }
            ])
            ->add('visible')
            ->add('pictureFiles', FileType::class, [
                'required' => false,
                'multiple' => true
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
