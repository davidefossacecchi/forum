<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity()]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: Types::STRING)]
#[ORM\DiscriminatorMap([
    'comment' => Comment::class,
    'reply' => CommentReply::class
])]
abstract class AbstractComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::TEXT)]
    private string $text;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    private User $author;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Gedmo\Timestampable(on: 'create')]
    private \DateTimeImmutable $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): AbstractComment
    {
        $this->id = $id;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): AbstractComment
    {
        $this->text = $text;
        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): AbstractComment
    {
        $this->author = $author;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): AbstractComment
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
