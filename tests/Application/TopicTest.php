<?php

namespace App\Tests\Application;

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
}