<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserProfileService
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;
    private PaginatorInterface $paginator;

    public function __construct(EntityManagerInterface $em,
                                UserPasswordHasherInterface $passwordHasher,
                                PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->paginator = $paginator;
    }

    public function getUserBooks(User $user, $page = 1, $limit = 4): PaginationInterface
    {
        $query = $this->em->getRepository(Book::class)->createQueryBuilder('b')
            ->where('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.id', 'DESC')
            ->getQuery();

        return $this->paginator->paginate(
            $query,
            $page,
            $limit
        );
    }

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