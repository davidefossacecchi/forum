<?php

namespace App\Security;

use App\Entity\UserOwnedEntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserOwnedEntityVoter extends Voter implements CacheableVoterInterface
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return 'delete' === $attribute && $subject instanceof UserOwnedEntityInterface;
    }

    /**
     * @param string $attribute
     * @param UserOwnedEntityInterface $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $subject->getAuthor() === $token->getUser();

    }
}
