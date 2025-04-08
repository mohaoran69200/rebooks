<?php

namespace App\Security\Voter;

use App\Entity\Book;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BookVoter extends Voter
{
    public const EDIT = 'BOOK_EDIT';
    public const DELETE = 'BOOK_DELETE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE], true) && $subject instanceof Book;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false; // L'utilisateur doit être connecté
        }

        /** @var Book $book */
        $book = $subject;

        // L'admin a tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Vérifier si l'utilisateur est l'auteur du livre
        return $user === $book->getUser();
    }
}
