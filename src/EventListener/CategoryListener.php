<?php

namespace App\EventListener;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postUpdate, method: 'clearCache', entity: Category::class)]
#[AsEntityListener(event: Events::postPersist, method: 'clearCache', entity: Category::class)]
class CategoryListener
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
    )
    {

    }

    public function clearCache(): void
    {
        $this->categoryRepository->clearCache();
    }
}
