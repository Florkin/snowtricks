<?php

namespace App\Listener;

use App\Entity\EmbedVideo;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class VideoEventSubscriber implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate'
        ];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof EmbedVideo) {
            return;
        }

        $youtubeId = $this->getYoutubeId($entity->getUrl());
        $entity->setYoutubeId($youtubeId);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof EmbedVideo) {
            return;
        }

        $youtubeId = $this->getYoutubeId($entity->getUrl());
        $entity->setYoutubeId($youtubeId);
    }

    public function getYoutubeId($url)
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        return $match[1];
    }
}