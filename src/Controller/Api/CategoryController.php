<?php

namespace App\Controller\Api;

use App\DTO\CategoryDto;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[Route(path: '/categories')]
class CategoryController extends AbstractFOSRestController
{
    #[Rest\Get(path: '', name: 'categories_index')]
    #[OA\Response(
        response: 200,
        description: 'Returns the selectable topic categories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: CategoryDto::class))
        )
    )]
    public function index(EntityManagerInterface $entityManager): View
    {
        /** @var CategoryRepository $repo */
        $repo = $entityManager->getRepository(Category::class);
        $all = $repo->getCategoryOptions();

        return $this->view($all);
    }
}
