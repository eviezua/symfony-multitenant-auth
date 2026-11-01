<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class AccountVoter extends Voter
{
    public const string CAN_ACCESS = 'CAN_ACCESS_ACCOUNT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::CAN_ACCESS === $attribute && (is_int($subject) || is_numeric($subject));
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $targetAccountId = (int)$subject;

        foreach ($user->getAccounts() as $account) {
            if ($account->getId() === $targetAccountId) {
                return true;
            }
        }

        return false;
    }
}
