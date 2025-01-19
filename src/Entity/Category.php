<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use phpDocumentor\Reflection\Type;

#[ORM\Entity]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::TEXT, length: 255)]
    private string $name;

    #[ORM\OneToMany(targetEntity: Topic::class, mappedBy: 'category')]
    #[Exclude]
    private Collection $topics;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function setId(int $id): Category
    {
        $this->id = $id;
        return $this;
    }

    public function setName(string $name): Category
    {
        $this->name = $name;
        return $this;
    }

    public function addTopic(Topic $topic): static
    {
        if (false === $this->topics->contains($topic)) {
            $this->topics->add($topic);
            $topic->setCategory($this);
        }
        return $this;
    }

    public function removeTopic(Topic $topic): static
    {
        $this->topics->remove($topic);
        return $this;
    }
}
