<?php

namespace App\Tests\Application;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait UrlGeneratorTrait
{
    protected Container $container;

    protected function generateUrl(string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        /** @var UrlGeneratorInterface $router */
        $router = $this->container->get(UrlGeneratorInterface::class);
        return $router->generate($name, $parameters, $referenceType);
    }
}