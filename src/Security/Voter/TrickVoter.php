<?php

namespace App\Security\Voter;

use App\Entity\Trick;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickVoter extends Voter
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
        return in_array($attribute, ['DELETE', 'EDIT'])
            && $subject instanceof Trick;
    }

    /**
     * @param string $attribute
     * @param Trick $trick
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $trick, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'DELETE':
                return $trick->getAuthor() == $user;
                break;

            case 'EDIT':
                return $user instanceof UserInterface;
                break;
        }

        return false;
    }
}
