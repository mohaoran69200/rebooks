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
    public function index(BookRepository $bookRepository, BookService $bookService, Request $request): Response
    {
        // Derniers livres ajoutés sans pagination (4 livres)
        $latestBooks = $bookRepository->findBy([], ['id' => 'DESC'], 4);

        // Livres avec pagination (8 livres par page)
        $page = $request->query->getInt('page', 1); // Page actuelle
        $books = $bookService->getPaginatedBooks($page, 8); // Limite à 8 livres par page

        // Calculer la pagination
        $totalBooks = count($books);  // Vous devez peut-être utiliser une autre méthode pour récupérer le total d'éléments
        $totalPages = ceil($totalBooks / 8); // Calculer le nombre total de pages

        // Créer un tableau de pages à afficher
        $pagesInRange = [];
        for ($i = 1; $i <= $totalPages; $i++) {
            $pagesInRange[] = $i;
        }

        $pagination = [
            'previousPage' => $page > 1 ? $page - 1 : null,
            'nextPage' => $page * 8 < $totalBooks ? $page + 1 : null,
            'pageParameterName' => 'page',
            'hasPreviousPage' => $page > 1,
            'hasNextPage' => $page * 8 < $totalBooks,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'pagesInRange' => $pagesInRange, // Ajoutez cette ligne pour transmettre les pages
        ];

        return $this->render('home/index.html.twig', [
            'latestBooks' => $latestBooks,
            'books' => $books,
            'pagination' => $pagination, // Passez la pagination à votre template
        ]);
    }
}

