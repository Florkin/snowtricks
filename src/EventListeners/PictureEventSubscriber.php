<?php

namespace App\Listener;

use App\Entity\Picture;
use App\Service\FileUploader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PictureEventSubscriber implements EventSubscriber
{
    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * PictureEventSubscriber constructor.
     * @param FileUploader $fileUploader
     */
    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function getSubscribedEvents()
    {
        return [
            'preRemove'
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
        $this->fileUploader->setTargetDirectory("images/tricks");
        $this->fileUploader->delete($entity->getFilename());
    }
}