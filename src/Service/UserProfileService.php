<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserProfileService
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

//    public function getUserBooks(User $user): array
//    {
//        return $this->em->getRepository('App:Book')->findBy(['user' => $user]);
//    }

    public function updateProfile(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function changePassword(User $user, string $oldPassword, string $newPassword): bool
    {
        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            return false;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        return true;
    }

    public function deleteAccount(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}