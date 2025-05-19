<?php

namespace App\Controller;

use App\Service\NYTService;
use App\Service\GoogleBooksService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class NYTController extends AbstractController
{
    public function __construct(
        private NYTService $nytService,
        private GoogleBooksService $googleBooksService
    ) {}

    #[Route('/api/nyt/bestsellers', name: 'nyt_bestsellers', methods: ['GET'])]
    public function getBestSellers(): JsonResponse
    {
        error_log("=== Début de la récupération des best-sellers NYT ===");
        try {
            // Récupérer les best-sellers du NYT
            error_log("Appel du service NYT pour récupérer les best-sellers");
            $bestSellers = $this->nytService->getCurrentBestSellers();
            error_log("Nombre de best-sellers récupérés : " . count($bestSellers));
            
            // Enrichir les données avec Google Books
            $enrichedBooks = [];
            foreach ($bestSellers as $book) {
                error_log("Traitement du livre : " . $book['title'] . " (ISBN: " . $book['isbn'] . ")");
                
                // Rechercher le livre dans Google Books par ISBN
                error_log("Recherche dans Google Books avec l'ISBN : " . $book['isbn']);
                $googleBooksResults = $this->googleBooksService->searchBooks('isbn:' . $book['isbn'], 'fr');
                
                if (!empty($googleBooksResults)) {
                    error_log("Livre trouvé dans Google Books");
                    $googleBook = $googleBooksResults[0];
                    $enrichedBooks[] = [
                        'nyt' => $book,
                        'googleBooks' => $googleBook
                    ];
                } else {
                    error_log("Livre non trouvé dans Google Books");
                    $enrichedBooks[] = [
                        'nyt' => $book,
                        'googleBooks' => null
                    ];
                }
            }

            error_log("Nombre total de livres enrichis : " . count($enrichedBooks));
            error_log("=== Fin de la récupération des best-sellers NYT ===");

            return $this->json([
                'status' => 'success',
                'data' => $enrichedBooks
            ]);
        } catch (\Exception $e) {
            error_log("ERREUR lors de la récupération des best-sellers : " . $e->getMessage());
            error_log("Stack trace : " . $e->getTraceAsString());
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/nyt/book/{isbn}', name: 'nyt_book_details', methods: ['GET'])]
    public function getBookDetails(string $isbn): JsonResponse
    {
        error_log("=== Début de la récupération des détails du livre (ISBN: {$isbn}) ===");
        try {
            // Récupérer les détails du livre depuis NYT
            error_log("Appel du service NYT pour récupérer les détails du livre");
            $nytDetails = $this->nytService->getBookDetailsByISBN($isbn);
            error_log("Détails NYT récupérés : " . ($nytDetails ? "Oui" : "Non"));
            
            // Récupérer les détails du livre depuis Google Books
            error_log("Recherche dans Google Books avec l'ISBN : " . $isbn);
            $googleBooksResults = $this->googleBooksService->searchBooks('isbn:' . $isbn, 'fr');
            $googleBookDetails = !empty($googleBooksResults) ? $googleBooksResults[0] : null;
            error_log("Détails Google Books récupérés : " . ($googleBookDetails ? "Oui" : "Non"));

            error_log("=== Fin de la récupération des détails du livre ===");

            return $this->json([
                'status' => 'success',
                'data' => [
                    'nyt' => $nytDetails,
                    'googleBooks' => $googleBookDetails
                ]
            ]);
        } catch (\Exception $e) {
            error_log("ERREUR lors de la récupération des détails du livre : " . $e->getMessage());
            error_log("Stack trace : " . $e->getTraceAsString());
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 