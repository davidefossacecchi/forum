<?php

namespace App\Tests\Application;

use App\DTO\CategoryDto;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends AbstractApplicationTest
{
    use UrlGeneratorTrait;
    use LogsUserInTrait;

    public function testCategoryIndex(): void
    {
        $this->logDefaultUserIn();
        $url = $this->generateUrl('categories_index');

        $this->client->jsonRequest('GET', $url);
        $this->assertResponseIsSuccessful();

        $categories = $this->getDecodedResponse();

        $this->assertNotEmpty($categories);

        foreach ($categories as $category) {
            $this->assertNotEmpty($category['id']);
            $this->assertNotEmpty($category['name']);
        }
    }

    public function testCategoryEdit(): void
    {
        $this->logDefaultUserIn();
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var CategoryRepository $categoryRepo */
        $categoryRepo = $em->getRepository(Category::class);

        $categories = $categoryRepo->findAll();
        /** @var Category $category */
        $category = $categories[array_rand($categories)];
        $url = $this->generateUrl('edit_category', ['category' => $category->getId()]);
        $text = 'prova';
        $this->client->jsonRequest('PATCH', $url, ['name' => $text]);

        $this->assertResponseIsSuccessful();
        /** @var Category $catReloaded */
        $em->refresh($category);
        $this->assertEquals($text, $category->getName());

        // check if the cache has been updated
        $cachedCategories = $categoryRepo->getCategoryOptions();

        /** @var CategoryDto $cachedCategory */
        foreach ($cachedCategories as $cachedCategory) {
            if ($cachedCategory->id === $category->getId()) {
                $this->assertEquals($text, $cachedCategory->name);
            }
        }
    }
}
