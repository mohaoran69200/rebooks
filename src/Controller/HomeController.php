<?php

namespace App\Controller;

use App\Service\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BookRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(BookRepository $bookRepository,
                          BookService $bookService,
                          Request $request): Response
    {
        // Derniers livres ajoutés sans pagination (4 livres)
        $latestBooks = $bookRepository->findBy([], ['id' => 'DESC'], 4);

        // Livres avec pagination (12 livres par page)
        $page = $request->query->getInt('page', 1); // Page actuelle
        $books = $bookService->getPaginatedBooks($page, 12); // Limite à 12 livres par page

        return $this->render('home/index.html.twig', [
            'latestBooks' => $latestBooks,
            'books' => $books, // L'objet de pagination complet
        ]);
    }
}

