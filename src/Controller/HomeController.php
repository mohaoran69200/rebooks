<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BookRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findBy([], ['id' => 'DESC'], 4);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'books' => $books,
        ]);
    }
}
