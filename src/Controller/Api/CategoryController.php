<?php

namespace App\Controller\Api;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/categories')]
class CategoryController extends AbstractFOSRestController
{
    #[Rest\Get(path: '', name: 'categories_index')]
    public function index(EntityManagerInterface $entityManager): View
    {
        $repo = $entityManager->getRepository(Category::class);
        $all = $repo->findAll();

        return $this->view($all);
    }
}
