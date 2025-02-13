<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProfileService
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    public function getUserBooks(User $user): array
    {
        return $this->em->getRepository('App:Book')->findBy(['user' => $user]);
    }

    public function updateProfile(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function changePassword(User $user, array $data): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['newPassword']));
        $this->em->persist($user);
        $this->em->flush();
    }

    public function deleteAccount(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}