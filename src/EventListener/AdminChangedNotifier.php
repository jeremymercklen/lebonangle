<?php

namespace App\EventListener;

// ...
use App\Entity\AdminUser;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: 'preUpdate', entity: AdminUser::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: AdminUser::class)]
class AdminChangedNotifier
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {

    }
    public function preUpdate(AdminUser $adminUser): void
    {
        if ($adminUser->getPlainPassword()) {
            $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, $adminUser->getPlainPassword()));
            $adminUser->setPlainPassword(null);
        }
    }
}
