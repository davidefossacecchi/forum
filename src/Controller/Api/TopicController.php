<?php

namespace App\Controller\Api;

use App\DTO\CategoryDto;
use App\DTO\TopicDto;
use App\Entity\Category;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Rest\Route('/topics')]
class TopicController extends AbstractFOSRestController
{
    use ChecksFormRequests;

    #[Rest\Post("", name: "create_topic")]
    public function create(Request $request, EntityManagerInterface $entityManager, #[CurrentUser] User $user): View
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $entityManager->getRepository(Category::class);

        $form = $this->createFormBuilder(new TopicDto())
            ->add('title', TextType::class)
            ->add('text', TextType::class)
            ->add('category',
                ChoiceType::class,
                [
                    'choices' => $categoryRepository->getCategoryOptions(),
                    'choice_label' => 'name',
                    'choice_value' => 'id'
                ])
            ->getForm();

        $form->submit($request->request->all());
        $this->throwExceptionIfInvalid($form);
        /** @var TopicDto $topicDto */
        $topicDto = $form->getData();
        $topic = new Topic();
        $topic->setTitle($topicDto->getTitle())
            ->setText($topicDto->getText())
            ->setCategory($categoryRepository->find($topicDto->getCategory()->id))
            ->setUser($user);
        $entityManager->persist($topic);
        $entityManager->flush();
        $entityManager->refresh($topic);
        return $this->view($topic);
    }

    #[Rest\Get("", name: 'list_topics')]
    public function index(Request $request, EntityManagerInterface $em): View
    {
        $page = $request->query->get('page', 1);
        /** @var TopicRepository $repo */
        $repo = $em->getRepository(Topic::class);
        $pg = $repo->getPage($page);
        return $this->view($pg);
    }

    #[Rest\Get("/{topic}", name: 'show_topic', requirements: ['topic' => '\d+'])]
    public function show(Topic $topic): View
    {
        return $this->view($topic);
    }

    #[Rest\Delete("/{topic}", name: "delete_topic", requirements: ['topic' => '\d+'])]
    #[IsGranted("delete", "topic")]
    public function delete(Topic $topic, EntityManagerInterface $entityManager): void
    {
        $entityManager->remove($topic);
        $entityManager->flush();
    }
}
