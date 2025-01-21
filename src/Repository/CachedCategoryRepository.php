<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


#[AsDecorator(decorates: CategoryRepository::class)]
class CachedCategoryRepository extends ServiceEntityRepository implements CachingRepositoryInterface
{
    private const CACHE_KEY = 'categories';
    public function __construct(
        #[AutowireDecorated] private readonly ServiceEntityRepository $innerRepo,
        private readonly CacheInterface $doctrineCache
    )
    {

    }

    public function findAll(): array
    {
        $all = $this->doctrineCache->get(self::CACHE_KEY, function (ItemInterface $item): array {
            return $this->innerRepo->findAll();
        });
        return $all;
    }

    public function __call(string $method, array $arguments): mixed
    {
        return call_user_func_array([$this->innerRepo, $method], $arguments);
    }

    public function clearCache(): void
    {
        $this->doctrineCache->delete(self::CACHE_KEY);
    }
}
