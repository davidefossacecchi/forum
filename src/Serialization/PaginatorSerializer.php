<?php

namespace App\Serialization;

use Doctrine\ORM\Tools\Pagination\Paginator;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginatorSerializer
{
    private readonly int $page;
    public function __construct(RequestStack $stack)
    {
        $this->page = $stack->getMainRequest()->query->get('page', 1);
    }

    public function serialize(JsonSerializationVisitor $visitor, Paginator $paginator): array
    {
        $data = iterator_to_array($paginator->getIterator());
        return [
            'data' => $visitor->visitArray($data, []),
            'pagination' => [
                'page' => $this->page,
                'length' => $paginator->getQuery()->getMaxResults(),
                'count' => $paginator->count()
            ]
        ];
    }
}
