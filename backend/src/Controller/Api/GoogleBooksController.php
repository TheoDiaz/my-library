<?php

namespace App\Controller\Api;

use App\Service\GoogleBooksService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/googlebooks')]
class GoogleBooksController extends AbstractController
{
    public function __construct(
        private readonly GoogleBooksService $googleBooksService
    ) {
    }

    #[Route('/search', name: 'api_googlebooks_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $maxResults = (int) $request->query->get('maxResults', 20);
        $lang = $request->query->get('lang', 'fr');

        try {
            error_log("Recherche Google Books - Query: " . $query);
            $results = $this->googleBooksService->searchBooks($query, $lang, $maxResults);
            error_log("Résultats trouvés: " . count($results));
            return new JsonResponse($results);
        } catch (\Exception $e) {
            error_log("Erreur lors de la recherche: " . $e->getMessage());
            return new JsonResponse([
                'error' => 'Une erreur est survenue lors de la recherche',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/details/{id}', name: 'api_googlebooks_details', methods: ['GET'])]
    public function getDetails(string $id): JsonResponse
    {
        try {
            $details = $this->googleBooksService->getBookDetails($id);
            if ($details === null) {
                return new JsonResponse(['error' => 'Livre non trouvé'], 404);
            }
            return new JsonResponse($details);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Une erreur est survenue lors de la récupération des détails',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 