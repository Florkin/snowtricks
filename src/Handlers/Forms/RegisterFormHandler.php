<?php

namespace App\Handlers\Forms;

use App\Handlers\Forms\AbstractFormHandler;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterFormHandler extends AbstractFormHandler
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
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * NewTrickFormHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, FileUploader $fileUploader, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->fileUploader = $fileUploader;
        $this->fileUploader->setTargetDirectory('uploads/images/avatars');
    }


    public function getFormType(): string
    {
        return $this->formType;
    }

    public function process($user): void
    {
        $newAvatar = $this->getForm()->get('avatarFilename')->getData();
        if ($newAvatar) {
            $fileName = $this->fileUploader->upload($newAvatar);
            $user->setAvatarFilename($fileName);
        }

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