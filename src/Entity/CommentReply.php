<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class CommentReply extends AbstractComment
{
    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'replies')]
    private Comment $comment;

    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function setComment(Comment $comment): CommentReply
    {
        $this->comment = $comment;
        $comment->addReply($this);
        return $this;
    }
}
