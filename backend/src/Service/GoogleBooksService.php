<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class GoogleBooksService
{
    private string $apiKey;
    private $httpClient;
    private string $coversDir;

    public function __construct(
        string $apiKey,
        ParameterBagInterface $params
    ) {
        $this->apiKey = $apiKey;
        $this->httpClient = HttpClient::create();
        $this->coversDir = $params->get('kernel.project_dir') . '/public/covers';
        
        // Créer le répertoire des couvertures s'il n'existe pas
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->coversDir)) {
            $filesystem->mkdir($this->coversDir, 0777);
            chmod($this->coversDir, 0777);
        }
    }

    private function downloadAndSaveCover(string $googleBooksId, ?string $coverUrl): ?string
    {
        if (!$coverUrl) {
            return null;
        }

        try {
            // Télécharger l'image
            $response = $this->httpClient->request('GET', $coverUrl);
            if ($response->getStatusCode() !== 200) {
                error_log("Erreur lors du téléchargement de la couverture: " . $response->getStatusCode());
                return null;
            }

            // Générer un nom de fichier unique
            $extension = pathinfo(parse_url($coverUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $googleBooksId . '.' . $extension;
            $filepath = $this->coversDir . '/' . $filename;

            // Sauvegarder l'image
            file_put_contents($filepath, $response->getContent());
            chmod($filepath, 0644);

            error_log("Couverture sauvegardée avec succès: " . $filepath);

            // Retourner le chemin relatif pour l'URL
            return '/covers/' . $filename;
        } catch (\Exception $e) {
            error_log("Erreur lors du téléchargement de la couverture: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Recherche de livres sur Google Books
     * @param string $query
     * @param string $lang
     * @param int $maxResults
     * @return array
     */
    public function searchBooks(string $query, string $lang = 'fr', int $maxResults = 20): array
    {
        $url = 'https://www.googleapis.com/books/v1/volumes';
        $params = [
            'q' => $query,
            'langRestrict' => $lang,
            'maxResults' => $maxResults,
            'key' => $this->apiKey,
        ];

        error_log("URL de recherche Google Books: " . $url);
        error_log("Paramètres: " . json_encode($params));

        try {
            $response = $this->httpClient->request('GET', $url, [
                'query' => $params
            ]);
            $data = $response->toArray();
            error_log("Réponse brute de Google Books: " . json_encode($data));

            // On mappe les résultats pour n'extraire que les infos utiles
            $books = [];
            foreach ($data['items'] ?? [] as $item) {
                $info = $item['volumeInfo'];
                $books[] = [
                    'id' => $item['id'],
                    'title' => $info['title'] ?? '',
                    'authors' => $info['authors'] ?? [],
                    'description' => $info['description'] ?? '',
                    'cover' => $info['imageLinks']['thumbnail'] ?? null,
                    'publishedDate' => $info['publishedDate'] ?? null,
                    'pageCount' => $info['pageCount'] ?? null,
                    'categories' => $info['categories'] ?? [],
                    'language' => $info['language'] ?? '',
                    'publisher' => $info['publisher'] ?? '',
                    'previewLink' => $info['previewLink'] ?? '',
                    'infoLink' => $info['infoLink'] ?? '',
                    'industryIdentifiers' => $info['industryIdentifiers'] ?? [],
                ];
            }
            error_log("Nombre de livres trouvés: " . count($books));
            return $books;
        } catch (\Exception $e) {
            error_log("Erreur lors de l'appel à l'API Google Books: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Détail d'un livre par son ID Google Books
     */
    public function getBookDetails(string $id): ?array
    {
        $url = "https://www.googleapis.com/books/v1/volumes/{$id}";
        $params = [
            'key' => $this->apiKey,
        ];
        $response = $this->httpClient->request('GET', $url, [
            'query' => $params
        ]);
        $data = $response->toArray(false);

        if (isset($data['error'])) {
            return null;
        }

        $info = $data['volumeInfo'] ?? [];
        $coverUrl = $info['imageLinks']['thumbnail'] ?? null;
        $localCoverPath = $this->downloadAndSaveCover($id, $coverUrl);

        return [
            'id' => $data['id'],
            'title' => $info['title'] ?? '',
            'authors' => $info['authors'] ?? [],
            'description' => $info['description'] ?? '',
            'cover' => $localCoverPath,
            'publishedDate' => $info['publishedDate'] ?? null,
            'pageCount' => $info['pageCount'] ?? null,
            'categories' => $info['categories'] ?? [],
            'language' => $info['language'] ?? '',
            'publisher' => $info['publisher'] ?? '',
            'previewLink' => $info['previewLink'] ?? '',
            'infoLink' => $info['infoLink'] ?? '',
            'industryIdentifiers' => $info['industryIdentifiers'] ?? [],
        ];
    }
}