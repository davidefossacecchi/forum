<?php

namespace App\DTO;

class CategoryDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name
    )
    {

    }

}