<?php

namespace App\Listener;

use App\Entity\User;
use App\Service\FileUploader;
use App\Service\ImageResizer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserEventSubscriber implements EventSubscriber
{
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var ImageResizer
     */
    private $imageResizer;

    /**
     * PictureEventSubscriber constructor.
     * @param FileUploader $fileUploader
     * @param ImageResizer $imageResizer
     */
    public function __construct(FileUploader $fileUploader, ImageResizer $imageResizer)
    {
        $this->fileUploader = $fileUploader;
        $this->imageResizer = $imageResizer;
    }

    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'postPersist',
            'postUpdate'
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Picture) {
            return;
        }
        $this->fileUploader->setTargetDirectory("uploads/images/avatars");
        $this->fileUploader->delete($entity->getFilename());
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            return;
        }
        if ($entity->getAvatarFilename()) {
            $this->resizeImage($entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return;
        }
        if ($entity->getAvatarFilename()) {
            $this->resizeImage($entity);
        }
    }

    private function resizeImage($entity)
    {
        $realpath = realpath("public/uploads/images/avatars/") ? realpath("public/uploads/images/avatars/") : realpath("uploads/images/avatars/");
        $this->imageResizer->resizeImage(
            $realpath,
            $entity->getAvatarFilename(),
            200,
            200
        );
    }
}