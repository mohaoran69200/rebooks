<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Book;
use App\Entity\User;
use App\Form\BookFormType;
use App\Security\Voter\BookVoter;
use App\Service\BookService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book', name: 'book_')]
class BookController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/add', name: 'add')]
    public function add(Request $request, BookService $bookService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $book = new Book();
        $book->setCreatedAt(new \DateTimeImmutable());
        $book->setUser($this->getUser()); // Associer l'utilisateur connecté

        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le nom de l'auteur directement
            $bookService->addBook($book);  // Ajoute le livre à la base de données
            $this->addFlash('success', 'Livre ajouté avec succès !');

            return $this->redirectToRoute('book_list');
        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/list', name: 'list')]
    public function list(BookService $bookService): Response
    {
        $books = $bookService->getAllBooks();

        return $this->render('book/list.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/{id}', name: 'show')]
    public function show(int $id, BookService $bookService): Response
    {
        $book = $bookService->getBookById($id);

        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id, Request $request, BookService $bookService): Response
    {
        $book = $bookService->getBookById($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        // Vérification avec le Voter
        $this->denyAccessUnlessGranted(BookVoter::EDIT, $book);

        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $bookService->updateBook($book); // Passer le livre
                $this->addFlash('success', 'Livre mis à jour avec succès !');
                return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
            } catch (Exception) {
                $this->addFlash('error', 'Erreur lors de la mise à jour du livre.');
            }

        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);

    }

    /**
     * @throws Exception
     */
    #[Route('/remove/{id}', name: 'remove', methods: ['POST'])]
    public function remove(int $id, Request $request, BookService $bookService): Response
    {
        // Récupération du livre
        $book = $bookService->getBookById($id);

        // Si le livre n'existe pas, on affiche une erreur 404
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        // Vérification des droits avec le BookVoter
        $this->denyAccessUnlessGranted(BookVoter::DELETE, $book);

        // Vérification du token CSRF
        $token = $request->request->get('_token');
        if (!$token || !$this->isCsrfTokenValid('delete' . $book->getId(), $token)) {
            $this->addFlash('error', 'Token CSRF invalide, suppression annulée.');
            return $this->redirectToRoute('book_list');
        }

        // Suppression du livre via le service
        $bookService->removeBook($book);
        $this->addFlash('success', 'Livre supprimé avec succès !');

        // Redirection vers la liste des livres après suppression
        return $this->redirectToRoute('book_list');
    }

}
