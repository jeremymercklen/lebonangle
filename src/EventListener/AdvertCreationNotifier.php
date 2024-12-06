<?php

namespace App\EventListener;

// ...
use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'preUpdate', entity: Advert::class)]
class AdvertCreationNotifier
{
    public function preUpdate(Advert $advert): void
    {
        $advert->setCreatedAt(new \DateTime());
    }
}