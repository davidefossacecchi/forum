<?php

namespace App\Security;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminActionVoter extends Voter implements CacheableVoterInterface
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['admin-edit', 'admin-create', 'admin-delete']);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        return in_array(UserRole::ADMIN->value, $user->getRoles());
    }

}
