<?php

namespace App\Controller\Api;

use App\DTO\CategoryDto;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;

#[Route(path: '/categories')]
class CategoryController extends AbstractFOSRestController
{
    use ChecksFormRequests;
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

    #[Rest\Patch('/{category}', name: 'edit_category')]
    #[IsGranted('admin-edit', 'category')]
    #[OA\Response(
        response: 200,
        description: 'Edit the category',
        content: new Model(type: Category::class)
    )]
    public function edit(
        EntityManagerInterface $entityManager,
        Request $request,
        Category $category
    ): View
    {
        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class, ['constraints' => new Assert\NotBlank()])
            ->getForm();

        $form->submit($request->request->all());

        $this->throwExceptionIfInvalid($form);

        $ct = $form->getData();
        $entityManager->persist($ct);
        $entityManager->flush();
        return $this->view($ct);
    }
}
