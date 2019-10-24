<?php


namespace App\Security;

use App\Entity\User as AppUser;
use Exception;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * Checks the user account before authentication.
     *
     * @param UserInterface $user
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }

    /**
     * Checks the user account after authentication.
     *
     * @param UserInterface $user
     * @throws Exception
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        // user account is expired, the user may be notified
        if (!$user->getEnabled()) {
            throw new \RuntimeException("Votre compte n'a pas été active ou n'est plus actif !");
        }
    }
}