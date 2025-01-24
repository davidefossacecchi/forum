<?php

namespace App\Tests\Application;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

abstract class AbstractApplicationTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected Container $container;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = static::getContainer();
    }

    protected function getDecodedResponse(): array
    {
        $responseContent = $this->client->getResponse()->getContent();
        return json_decode($responseContent, true, JSON_THROW_ON_ERROR);
    }
}