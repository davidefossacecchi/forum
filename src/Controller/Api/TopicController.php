<?php

namespace App\Controller\Api;

use App\DTO\FileUploadDTO;
use App\DTO\TopicDto;
use App\Entity\Category;
use App\Entity\Topic;
use App\Entity\User;
use App\Forms\TopicType;
use App\Repository\CategoryRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes\Property;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Rest\Route('/topics')]
class TopicController extends AbstractFOSRestController
{
    use ChecksFormRequests;

    #[Rest\Post("", name: "create_topic")]
    #[OA\Response(
        response: 200,
        description: 'Creates a topic',
        content: new Model(type: Topic::class)
    )]
    #[OA\RequestBody(
        content: new Model(type: TopicType::class)
    )]
    public function create(Request $request, EntityManagerInterface $entityManager, #[CurrentUser] User $user): View
    {
        $form = $this->createForm(TopicType::class, new TopicDto());
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $entityManager->getRepository(Category::class);

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
    #[OA\Response(
        response: 200,
        description: 'Returns the topics list',
        content: new OA\JsonContent(
            properties: [
                new Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Topic::class))
                ),
                new Property(
                    property: 'pagination',
                    properties: [
                        new Property(property: 'page', type: 'integer'),
                        new Property(property: 'length', type: 'integer'),
                        new Property(property: 'count', type: 'integer'),
                    ]
                )
            ]
        )
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'The page of the topics to fetch',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    public function index(Request $request, EntityManagerInterface $em): View
    {
        $page = $request->query->get('page', 1);
        /** @var TopicRepository $repo */
        $repo = $em->getRepository(Topic::class);
        $pg = $repo->getPage($page);
        return $this->view($pg);
    }

    #[Rest\Get("/{topic}", name: 'show_topic', requirements: ['topic' => '\d+'])]
    #[OA\Response(
        response: 200,
        description: 'Gets a single topic',
        content: new Model(type: Topic::class)
    )]
    public function show(Topic $topic): View
    {
        return $this->view($topic);
    }

    #[Rest\Delete("/{topic}", name: "delete_topic", requirements: ['topic' => '\d+'])]
    #[IsGranted("delete", "topic")]
    #[OA\Response(
        response: 200,
        description: 'Deletes a single topic'
    )]
    public function delete(Topic $topic, EntityManagerInterface $entityManager): void
    {
        $entityManager->remove($topic);
        $entityManager->flush();
    }
}
