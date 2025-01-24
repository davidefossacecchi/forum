<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
class TopicDto
{

    #[Assert\NotBlank]
    private string $title;

    #[Assert\NotBlank]
    private string $text;

    #[Assert\NotBlank]
    private CategoryDto $category;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getCategory(): CategoryDto
    {
        return $this->category;
    }

    public function setCategory(CategoryDto $category): void
    {
        $this->category = $category;
    }
}