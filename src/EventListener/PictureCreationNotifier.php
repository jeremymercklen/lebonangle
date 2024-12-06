<?php

namespace App\EventListener;

// ...
use App\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Picture::class)]
class PictureCreationNotifier
{
    public function prePersist(Picture $advert): void
    {
        $advert->setCreatedAt(new \DateTime());
    }
}