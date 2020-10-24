<?php

namespace App\Handlers\Forms;

use App\Handlers\Forms\AbstractFormHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFormHandler extends AbstractFormHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $formType;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * NewTrickFormHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }


    public function getFormType(): string
    {
        return $this->formType;
    }

    public function process($user): void
    {
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $this->getForm()->get('plainPassword')->getData()
            )
        );
        $user->setRoles(["ROLE_USER"]);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function setFormType(?string $formType): void
    {
        $this->formType = $formType;
    }
}