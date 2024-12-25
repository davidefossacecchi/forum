<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment extends AbstractComment
{
    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: 'comments')]
    #[Exclude]
    private Topic $topic;

    #[ORM\OneToMany(targetEntity: CommentReply::class, mappedBy: 'comment', orphanRemoval: true)]
    private Collection $replies;

    public function __construct()
    {
        $this->replies = new ArrayCollection();
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }

    public function setTopic(Topic $topic): Comment
    {
        $this->topic = $topic;
        $topic->addComment($this);
        return $this;
    }

    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(CommentReply $reply): Comment
    {
        if (false === $this->replies->contains($reply)) {
            $this->replies->add($reply);
            $reply->setComment($this);
        }

        return $this;
    }

    public function removeReply(CommentReply $reply): Comment
    {
        $this->replies->remove($reply);
        return $this;
    }
}
