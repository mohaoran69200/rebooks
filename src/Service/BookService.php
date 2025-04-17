<?php

namespace App\Service;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Knp\Component\Pager\PaginatorInterface;

class BookService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private PaginatorInterface $paginator;

    public function __construct(EntityManagerInterface $entityManager,
                                LoggerInterface $logger,
                                PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->paginator = $paginator;
    }


    /**
     * @throws Exception
     */
    public function addBook(Book $book): void
    {
        try {
            $this->entityManager->persist($book);
            $this->entityManager->flush();
        } catch (Exception $e) {
            $this->logger->error('Erreur lors de l\'ajout du livre : ' . $e->getMessage());
            throw new Exception('Impossible d\'ajouter le livre');
        }
    }

    public function getPaginatedBooks($page = 1, $limit = 12): \Knp\Component\Pager\Pagination\PaginationInterface
    {
        $query = $this->entityManager->getRepository(Book::class)->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->getQuery();

        return $this->paginator->paginate(
            $query,
            $page,
            $limit
        );
    }

    public function getAllBooks(): array
    {
        return $this->entityManager->getRepository(Book::class)->findAll();
    }

    /**
     * @throws Exception
     */
    public function getBookById(int $id): Book
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            throw new Exception('Livre introuvable.');
        }
        return $book;
    }


    /**
     * @throws Exception
     */
    public function updateBook(Book $book): void
    {
        try {
            $this->entityManager->flush();
        } catch (Exception $e) {
            $this->logger->error('Erreur lors de la mise à jour du livre : ' . $e->getMessage());
            throw new Exception('Impossible de mettre à jour le livre');
        }
    }


    /**
     * @throws Exception
     */
    public function removeBook(Book $book): void
    {
        try {
            $this->entityManager->remove($book);
            $this->entityManager->flush();
        } catch (Exception $e) {
            $this->logger->error('Erreur lors de la suppression du livre : ' . $e->getMessage());
            throw new Exception('Impossible de supprimer le livre');
        }
    }
}

