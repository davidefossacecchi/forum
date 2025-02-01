<?php

namespace App\Forms;

use App\DTO\TopicDto;
use App\Entity\Category;
use App\Entity\Topic;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicType extends AbstractType
{
    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => TopicDto::class]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->em->getRepository(Category::class);
        $builder
            ->add('title', TextType::class)
            ->add('text', TextType::class)
            ->add('category',
                ChoiceType::class,
                [
                    'choices' => $categoryRepository->getCategoryOptions(),
                    'choice_label' => 'name',
                    'choice_value' => 'id'
                ]);
    }


}
