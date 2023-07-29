<?php

namespace App\Security;

use App\Entity\CortestUser;
use App\Entity\Resource;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DeleteResourceVoter extends Voter
{
    const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::DELETE == $attribute && $subject instanceof Resource;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var CortestUser $user */
        $user = $token->getUser();

        if (!$user instanceof CortestUser) {
            throw new LogicException("Unreachable");
        }

        if ($attribute != self::DELETE) {
            throw new LogicException("Unreachable");
        }

        if (!$subject instanceof Resource) {
            throw new LogicException("Unreachable");
        }

        return $user->id == $subject->user->id;
    }

}