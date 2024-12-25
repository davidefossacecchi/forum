<?php

namespace App\Controller\Api;

use App\Entity\Topic;
use App\Entity\User;
use App\Pagination\Pagination;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Rest\Route('/topics')]
class TopicController extends AbstractFOSRestController
{
    use ChecksFormRequests;

    #[Rest\Post("", name: "create_topic")]
    public function create(Request $request, EntityManagerInterface $entityManager, #[CurrentUser] User $user): View
    {
        $form = $this->createFormBuilder(new Topic())
            ->add('title', TextType::class)
            ->add('text', TextType::class)
            ->getForm();

        $form->submit($request->request->all());
        $this->throwExceptionIfInvalid($form);
        $topic = $form->getData();
        $topic->setUser($user);
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
}
