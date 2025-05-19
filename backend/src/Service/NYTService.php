<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NYTService
{
    private string $apiKey;
    private $httpClient;
    private const BASE_URL = 'https://api.nytimes.com/svc/books/v3';

    public function __construct(
        string $apiKey
    ) {
        $this->apiKey = $apiKey;
        $this->httpClient = HttpClient::create();
    }

    /**
     * Récupère la liste des best-sellers actuels
     * @return array
     */
    public function getCurrentBestSellers(): array
    {
        $url = self::BASE_URL . '/lists/current/hardcover-fiction.json';
        
        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => [
                    'api-key' => $this->apiKey
                ]
            ]);

            $data = $response->toArray();
            
            if (!isset($data['results']['books'])) {
                throw new \Exception('Format de réponse NYT invalide');
            }

            // On extrait uniquement les ISBN des livres
            $books = [];
            foreach ($data['results']['books'] as $book) {
                if (isset($book['primary_isbn13'])) {
                    $books[] = [
                        'isbn' => $book['primary_isbn13'],
                        'title' => $book['title'],
                        'author' => $book['author'],
                        'rank' => $book['rank']
                    ];
                }
            }

            return $books;
        } catch (\Exception $e) {
            error_log("Erreur lors de l'appel à l'API NYT: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupère les détails d'un livre par son ISBN
     * @param string $isbn
     * @return array|null
     */
    public function getBookDetailsByISBN(string $isbn): ?array
    {
        $url = self::BASE_URL . '/reviews.json';
        
        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => [
                    'api-key' => $this->apiKey,
                    'isbn' => $isbn
                ]
            ]);

            $data = $response->toArray();
            
            if (!isset($data['results'][0])) {
                return null;
            }

            $review = $data['results'][0];
            return [
                'title' => $review['book_title'],
                'author' => $review['book_author'],
                'summary' => $review['summary'],
                'review' => $review['review'],
                'url' => $review['url']
            ];
        } catch (\Exception $e) {
            error_log("Erreur lors de l'appel à l'API NYT pour l'ISBN {$isbn}: " . $e->getMessage());
            return null;
        }
    }
} 