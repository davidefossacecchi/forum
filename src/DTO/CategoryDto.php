<?php

namespace App\DTO;

readonly class CategoryDto
{
    public function __construct(
        public int    $id,
        public string $name
    )
    {

    }

}
