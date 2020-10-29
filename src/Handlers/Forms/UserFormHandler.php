<?php

namespace App\Handlers\Forms;

use App\Handlers\Forms\AbstractFormHandler;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

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
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * NewTrickFormHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     */
    public function __construct(EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
        $this->fileUploader->setTargetDirectory('uploads/images/avatars');
    }


    public function getFormType(): string
    {
        return $this->formType;
    }

    public function process($data): void
    {
        $newAvatar = $this->getForm()->get('avatarFilename')->getData();
        if ($newAvatar) {
            $fileName = $this->fileUploader->upload($newAvatar);
            if ($data->getAvatarFilename() != null) {
                $this->fileUploader->delete($data->getAvatarFilename());
            }
            $data->setAvatarFilename($fileName);
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function setFormType(?string $formType): void
    {
        $this->formType = $formType;
    }


}