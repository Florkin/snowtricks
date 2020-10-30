<?php

namespace App\Listener;

use App\Entity\Picture;
use App\Service\FileUploader;
use App\Service\ImageResizer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PictureEventSubscriber implements EventSubscriber
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
            'postPersist'
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
        $this->fileUploader->setTargetDirectory("uploads/images/tricks");
        $this->fileUploader->delete($entity->getFilename());
        $this->fileUploader->setTargetDirectory("uploads/images/tricks/thumbs");
        $this->fileUploader->delete($entity->getFilename());
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Picture) {
            return;
        }
        $realpath = realpath("public/uploads/images/tricks/") ? realpath("public/uploads/images/tricks/") : realpath("uploads/images/tricks/");

        $this->imageResizer->resizeImage(
            $realpath,
            $entity->getFilename(),
            348,
            261,
            true
        );

        $this->imageResizer->resizeImage(
            $realpath,
            $entity->getFilename(),
            1280,
            720
        );
    }
}