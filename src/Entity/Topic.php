<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;


#[Entity]
class Topic
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $text;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'topics')]
    private User $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Topic
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Topic
    {
        $this->title = $title;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): Topic
    {
        $this->text = $text;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Topic
    {
        $this->user = $user;
        return $this;
    }
}
