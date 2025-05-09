<?php

namespace App\Controller\Api;

use App\Service\OpenLibraryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookSearchController extends AbstractController
{
    public function __construct(
        private readonly OpenLibraryService $openLibraryService
    ) {
    }

    #[Route('/api/books/search', name: 'api_books_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q');

        if (empty($query)) {
            return new JsonResponse(['error' => 'Le paramètre de recherche "q" est requis'], 400);
        }

        $results = $this->openLibraryService->searchBooks($query);

        // Convertir les DTOs en tableaux pour la réponse JSON
        return new JsonResponse(
            array_map(fn ($result) => $result->toArray(), $results)
        );
    }
} 