<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    #[Route('/books', name: 'books_index', methods: ['GET'])]
    public function index(Request $request, BookRepository $bookRepository): JsonResponse
    {
        $query = $request->query->get('q');
        $authorRaw = $request->query->get('author');
        $authorId = (is_numeric($authorRaw) && (int) $authorRaw > 0) ? (int) $authorRaw : null;

        $books = $bookRepository->findBySearchAndAuthor($query, $authorId);

        $data = array_map(fn (Book $book) => $this->serializeBook($book), $books);

        return $this->json([
            'count' => count($data),
            'items' => $data,
        ]);
    }

    #[Route('/books/{id}', name: 'books_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Book $book): JsonResponse
    {
        return $this->json($this->serializeBook($book));
    }

    private function serializeBook(Book $book): array
    {
        $author = $book->getAuthor();

        return [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'publishedYear' => $book->getPublishedYear(),
            'pages' => $book->getPages(),
            'description' => $book->getDescription(),
            'author' => $author ? [
                'id' => $author->getId(),
                'name' => $author->getName(),
                'country' => $author->getCountry(),
                'birthYear' => $author->getBirthYear(),
            ] : null,
        ];
    }
}