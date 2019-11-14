<?php

namespace App\Security\Voter;

use App\Entity\Trick;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['TRICK_EDIT', 'TRICK_DELETE'])
            && $subject instanceof Trick;
    }

    /**
     * @param string $attribute
     * @param Trick $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'TRICK_EDIT':
            case 'TRICK_DELETE':
            // this is the author!
            if ($subject->getAuthor() === $user) {
                return true;
            }
            // this is the administrator!
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return true;
            }
                break;
        }

        return false;
    }
}
