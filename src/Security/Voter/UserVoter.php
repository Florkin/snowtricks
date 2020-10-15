<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    /**
     * @var Security
     */
    private $security;

    /**
     * TrickVoter constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT_USER', 'DELETE_USER', 'EDIT_PASSWORD'])
            && $subject instanceof User;
    }

    /**
     * @param string $attribute
     * @param User $user
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $user, TokenInterface $token)
    {
        $actualUser = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'EDIT_USER':
            case 'DELETE_USER':
                return $actualUser == $user || $this->security->isGranted('ROLE_ADMIN');
                break;

            case 'EDIT_PASSWORD':
                return $actualUser == $user;
                break;
        }

        return false;
    }
}
