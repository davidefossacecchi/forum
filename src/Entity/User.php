<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;
    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Exclude]
    private string $password;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/[A-Z][a-z]+( [A-Z][a-z]+)*/', message: 'Nome non valido')]
    private string $name;

    #[ORM\OneToMany(targetEntity: Topic::class, mappedBy: 'user')]
    #[Exclude]
    private Collection $topics;

    #[Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Exclude]
    private \DateTimeImmutable $createdAt;


    #[Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Exclude]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(targetEntity: AbstractComment::class, mappedBy: 'author')]
    #[Exclude]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'followed')]
    #[ORM\JoinTable(
        name: 'user_follower',
        joinColumns: new ORM\JoinColumn(name: 'followed_id', referencedColumnName: 'id'),
        inverseJoinColumns: new ORM\JoinColumn(name: 'follower_id', referencedColumnName: 'id')
    )]
    #[Exclude]
    private Collection $followers;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'followers')]
    #[Exclude]
    private Collection $followed;

    #[Exclude]
    private ?string $plainPassword = null;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->followed = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return ['ROLE_USER', 'ROLE_API'];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function setTopics(Collection $topics): User
    {
        $this->topics = $topics;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(User $user): User
    {
        if ($user->getId() === $this->getId()) {
            throw new \InvalidArgumentException('A user cannot follow it self');
        }

        if (false === $this->followers->contains($user)) {
            $this->followers->add($user);
            $user->addFollowed($this);
        }

        return $this;
    }

    public function getFollowed(): Collection
    {
        return $this->followed;
    }

    public function addFollowed(User $user): User
    {
        if ($user->getId() === $this->getId()) {
            throw new \InvalidArgumentException('A user cannot follow it self');
        }

        if (false === $this->followed->contains($user)) {
            $this->followed->add($user);
            $user->addFollower($this);
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): User
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): User
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
