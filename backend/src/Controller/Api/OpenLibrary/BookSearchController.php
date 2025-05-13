<?php

namespace App\Controller\Api\OpenLibrary;

use App\Service\OpenLibraryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;

#[Route('/api/openlibrary')]
class BookSearchController extends AbstractController
{
    public function __construct(
        private readonly OpenLibraryService $openLibraryService,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/search', name: 'api_openlibrary_search', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function search(Request $request): JsonResponse
    {
        $this->logger->info('Tentative d\'accès à la recherche de livres OpenLibrary');
        
        try {
            $user = $this->getUser();
            $this->logger->info('Utilisateur connecté', [
                'user' => $user ? $user->getUserIdentifier() : null,
                'roles' => $user ? $user->getRoles() : []
            ]);
            
            $query = $request->query->get('q');

            if (empty($query)) {
                return new JsonResponse(['error' => 'Le paramètre de recherche "q" est requis'], 400);
            }

            $results = $this->openLibraryService->searchBooks($query);

            return new JsonResponse(
                array_map(fn ($result) => $result->toArray(), $results)
            );
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la recherche de livres', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    #[Route('/details/{id}', name: 'api_openlibrary_details', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function getDetails(string $id): JsonResponse
    {
        try {
            $details = $this->openLibraryService->getBookDetails($id);
            return new JsonResponse($details);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Livre non trouvé'], 404);
        }
    }

    #[Route('/isbn/{isbn}', name: 'api_openlibrary_search_isbn', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function searchByIsbn(string $isbn): JsonResponse
    {
        try {
            $results = $this->openLibraryService->searchByIsbn($isbn);
            return new JsonResponse(
                array_map(fn ($result) => $result->toArray(), $results)
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Livre non trouvé'], 404);
        }
    }
}