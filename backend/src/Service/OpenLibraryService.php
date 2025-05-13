<?php

namespace App\Service;

use App\DTO\BookSearchResult;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenLibraryService
{
    private const BASE_URL = 'https://openlibrary.org';
    private const CACHE_TTL = 3600; // 1 heure

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache
    ) {
    }

    /**
     * Recherche des livres sur Open Library
     * 
     * @param string $query La requête de recherche
     * @return array<BookSearchResult> Les résultats de la recherche
     */
    public function searchBooks(string $query): array
    {
        return $this->cache->get(
            'book_search_' . md5($query),
            function () use ($query) {
                $response = $this->httpClient->request('GET', self::BASE_URL . '/search.json', [
                    'query' => [
                        'q' => $query
                    ]
                ]);

                $data = $response->toArray();

                // On transforme chaque résultat en DTO
                return array_map(
                    fn (array $book) => BookSearchResult::fromArray([
                        'title' => $book['title'] ?? null,
                        'author_name' => $book['author_name'][0] ?? null,
                        'first_publish_year' => $book['first_publish_year'] ?? null,
                        'cover_i' => $book['cover_i'] ?? null,
                        'isbn' => $book['isbn'][0] ?? null,
                    ]),
                    $data['docs'] ?? []
                );
            }
        );
    }

    /**
     * Récupère les détails d'un livre par son ID Open Library
     * 
     * @param string $id L'ID du livre sur Open Library
     * @return array Les détails du livre
     */
    public function getBookDetails(string $id): array
    {
        return $this->cache->get(
            'book_details_' . $id,
            function () use ($id) {
                $response = $this->httpClient->request('GET', self::BASE_URL . '/works/' . $id . '.json');
                return $response->toArray();
            }
        );
    }

    /**
     * Recherche un livre par son ISBN
     * 
     * @param string $isbn L'ISBN du livre
     * @return array<BookSearchResult> Les résultats de la recherche
     */
    public function searchByIsbn(string $isbn): array
    {
        return $this->cache->get(
            'book_isbn_' . $isbn,
            function () use ($isbn) {
                $response = $this->httpClient->request('GET', self::BASE_URL . '/isbn/' . $isbn . '.json');
                $data = $response->toArray();

                return [BookSearchResult::fromArray([
                    'title' => $data['title'] ?? null,
                    'author_name' => $data['authors'][0]['name'] ?? null,
                    'first_publish_year' => $data['publish_date'] ?? null,
                    'cover_i' => $data['covers'][0] ?? null,
                    'isbn' => $isbn,
                ])];
            }
        );
    }
} 