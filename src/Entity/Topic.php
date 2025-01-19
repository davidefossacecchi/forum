<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: TopicRepository::class)]
class Topic implements UserOwnedEntityInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $text;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'topics')]
    private User $user;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'topic', orphanRemoval: true)]
    #[Exclude]
    private Collection $comments;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'topics')]
    #[Assert\NotNull(message: 'La categoria deve essere specificata')]
    private Category $category;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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

    public function getAuthor(): User
    {
        return $this->getUser();
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): Topic
    {
        $this->category = $category;
        $category->addTopic($this);
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): Topic
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): Topic
    {
        if (false === $this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTopic($this);
        }
        return $this;
    }

    public function removeComment(Comment $comment): Topic
    {
        $this->comments->removeElement($comment);
        return $this;
    }
}
