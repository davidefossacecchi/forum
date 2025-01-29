<?php

namespace App\Tests\Application;

use App\Entity\Category;
use App\Entity\Topic;
use App\Repository\CategoryRepository;
use App\Repository\TopicRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class TopicTest extends AbstractApplicationTest
{
    use LogsUserInTrait;
    use UrlGeneratorTrait;

    public function testTopicList(): void
    {
        $this->logDefaultUserIn();
        $url = $this->generateUrl('list_topics');
        $this->client->jsonRequest('GET', $url);
        $this->assertResponseIsSuccessful();

        $decodedResponse = $this->getDecodedResponse();
        $this->assertNotEmpty($decodedResponse['data']);
        $this->assertNotEmpty($decodedResponse['pagination']);
        $this->assertEquals(1, $decodedResponse['pagination']['page']);
    }

    public function testTopicRetrieve(): void
    {
        $this->logDefaultUserIn();
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var ServiceEntityRepository $topicRepo */
        $topicRepo = $em->getRepository(Topic::class);
        /** @var Topic[] $topics */
        $topics = $topicRepo->findAll();

        $topic = $topics[array_rand($topics)];
        $url = $this->generateUrl('show_topic', ['topic' => $topic->getId()]);
        $this->client->jsonRequest('GET', $url);
        $this->assertResponseIsSuccessful();

        $response = $this->getDecodedResponse();
        $this->assertEquals($topic->getId(), $response['id']);
    }

    public function testTopicCreation(): void
    {
        $testTitle = 'test title 123';
        $testBody = 'test body 123';

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $em->getRepository(Category::class);

        $categoryOptions = $categoryRepository->getCategoryOptions();

        $selectedCategory = $categoryOptions[array_rand($categoryOptions)];

        $this->logDefaultUserIn();

        $url = $this->generateUrl('create_topic');

        $this->client->jsonRequest('POST', $url, ['title' => $testTitle, 'text' => $testBody, 'category' => $selectedCategory->id]);

        $this->assertResponseIsSuccessful();

        $response = $this->getDecodedResponse();

        $this->assertNotEmpty($response['id']);

        /** @var TopicRepository $topicRepository */
        $topicRepository = $em->getRepository(Topic::class);

        $topic = $topicRepository->find($response['id']);

        $this->assertNotEmpty($topic);
    }

    public function testTopicDeletableByOwner(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);

        /** @var TopicRepository $topicRepository */
        $topicRepository = $em->getRepository(Topic::class);

        $topics = $topicRepository->findAll();

        /** @var Topic $topic */
        $topic = $topics[array_rand($topics)];

        $this->client->loginUser($topic->getAuthor());

        $topicId = $topic->getId();
        $url = $this->generateUrl('delete_topic', ['topic' => $topicId]);

        $this->client->jsonRequest('DELETE', $url);

        $this->assertResponseIsSuccessful();

        $persistedTopic = $topicRepository->find($topicId);

        $this->assertNull($persistedTopic);
    }

    public function testTopicNotDeletableByNonOwner(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);

        /** @var TopicRepository $topicRepository */
        $topicRepository = $em->getRepository(Topic::class);
        $user = $this->getDefaultUser();
        $topic = $topicRepository->createQueryBuilder('t')
            ->where('t.user != :user')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();



        $this->client->loginUser($user);

        $topicId = $topic->getId();
        $url = $this->generateUrl('delete_topic', ['topic' => $topicId]);

        $this->client->jsonRequest('DELETE', $url);

        $this->assertResponseStatusCodeSame(403);

        $persistedTopic = $topicRepository->find($topicId);

        $this->assertNotEmpty($persistedTopic);
    }
}
