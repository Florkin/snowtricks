<?php

namespace App\Handlers\Forms;

use App\Entity\User;
use App\Form\Model\ChangePassword;
use App\Form\PasswordFormType;
use App\Handlers\Forms\AbstractFormHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordFormHandler extends AbstractFormHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var FlashBagInterface
     */
    private $flash;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var string
     */
    private $user;

    /**
     * NewTrickFormHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FlashBagInterface $flash
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, FlashBagInterface $flash)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->flash = $flash;
    }


    public function getFormType(): string
    {
        return PasswordFormType::class;
    }

    public function process($changePassword): void
    {
        $user = $this->getUser();
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $changePassword->getNewPassword()
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }


    public function setFormType(string $formType): void
    {
        $this->formType = $formType;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }


}