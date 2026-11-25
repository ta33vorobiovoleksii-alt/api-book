<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    public function index(
        Request $request,
        BookRepository $bookRepository,
        AuthorRepository $authorRepository
    ): Response {
        $query = $request->query->get('q');
        $authorRaw = $request->query->get('author');
        $authorId = (is_numeric($authorRaw) && (int) $authorRaw > 0) ? (int) $authorRaw : null;

        $books = $bookRepository->findBySearchAndAuthor($query, $authorId);
        $authors = $authorRepository->findBy([], ['name' => 'ASC']);

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'authors' => $authors,
            'currentQuery' => $query,
            'currentAuthor' => $authorId,
        ]);
    }

    #[Route('/book/{id}', name: 'app_book_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}