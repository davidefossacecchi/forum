<?php

namespace App\Repository;

use App\DTO\CategoryDto;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


class CategoryRepository extends ServiceEntityRepository implements CachingServiceInterface
{
    private const CACHE_KEY = 'categories.options.2';

    private const CACHE_EXPIRATION = 7200;
    public function __construct(
        ManagerRegistry $registry,
        private readonly CacheInterface $dataCache,
        private readonly DenormalizerInterface & NormalizerInterface $normalizer
    )
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return CategoryDto[]
     */
    public function getCategoryOptions(): array
    {
        $data = $this->dataCache->get(self::CACHE_KEY, function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_EXPIRATION);
            $items = $this->findAll();
            return $this->normalizer->normalize($items, context:[AbstractNormalizer::IGNORED_ATTRIBUTES => ['topics']]);
        });
        return $this->normalizer->denormalize($data, CategoryDto::class.'[]');
    }

    public function clearCache(): void
    {
        $this->dataCache->delete(self::CACHE_KEY);
    }


}
