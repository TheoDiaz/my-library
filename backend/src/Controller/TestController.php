<?php

namespace App\Controller;

use App\Service\OpenLibraryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/test')]
class TestController extends AbstractController
{
    public function __construct(private readonly OpenLibraryService $openLibraryService)
    {
    }

    #[Route('/search/{query}', name: 'test_search')]
    public function search(string $query): JsonResponse
    {
        $results = $this->openLibraryService->searchBooks($query);
        return $this->json(array_map(fn($book) => $book->toArray(), $results));
    }

    #[Route('/isbn/{isbn}', name: 'test_isbn')]
    public function searchByIsbn(string $isbn): JsonResponse
    {
        $results = $this->openLibraryService->searchByIsbn($isbn);
        return $this->json(array_map(fn($book) => $book->toArray(), $results));
    }

    #[Route('/book/{id}', name: 'test_book_details')]
    public function getBookDetails(string $id): JsonResponse
    {
        $details = $this->openLibraryService->getBookDetails($id);
        return $this->json($details);
    }
} 