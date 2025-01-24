<?php

namespace App\Tests\Application;

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
}