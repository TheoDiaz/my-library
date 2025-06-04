<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\LibraryBook;
use App\Entity\Wishlist;
use App\Service\GoogleBooksService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/library')]
#[IsGranted('ROLE_USER')]
class LibraryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GoogleBooksService $googleBooksService
    ) {}

    #[Route('/add', name: 'library_add', methods: ['POST'])]
    public function addToLibrary(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['googleBooksId'])) {
            return $this->json(['error' => 'googleBooksId is required'], 400);
        }

        // Récupérer les détails du livre depuis Google Books
        $bookDetails = $this->googleBooksService->getBookDetails($data['googleBooksId']);
        if (!$bookDetails) {
            return $this->json(['error' => 'Book not found in Google Books'], 404);
        }

        // Vérifier si le livre existe déjà dans notre base
        $existingBook = $this->entityManager->getRepository(Book::class)->findOneBy([
            'googleBooksId' => $data['googleBooksId']
        ]);

        if (!$existingBook) {
            // Créer un nouveau livre
            $existingBook = new Book();
            $existingBook->setTitle($bookDetails['title']);
            $existingBook->setAuthor($bookDetails['authors'][0] ?? null);
            $existingBook->setFirstPublishYear($bookDetails['publishedDate'] ? (int)substr($bookDetails['publishedDate'], 0, 4) : null);
            $existingBook->setCover($bookDetails['cover']);
            $existingBook->setIsbn($bookDetails['industryIdentifiers'][0]['identifier'] ?? null);
            $existingBook->setGoogleBooksId($data['googleBooksId']);
            $existingBook->setOwner($this->getUser());

            $this->entityManager->persist($existingBook);
        }

        // Vérifier si l'utilisateur a déjà ce livre dans sa bibliothèque
        $existingLibraryBook = $this->entityManager->getRepository(LibraryBook::class)->findOneBy([
            'user' => $this->getUser(),
            'book' => $existingBook
        ]);

        if ($existingLibraryBook) {
            return $this->json(['error' => 'Book already in your library'], 400);
        }

        // Créer le lien dans la bibliothèque
        $libraryBook = new LibraryBook();
        $libraryBook->setUser($this->getUser());
        $libraryBook->setBook($existingBook);
        $libraryBook->setStatus($data['status'] ?? 'to_read');
        
        if ($data['status'] === 'reading') {
            $libraryBook->setStartDate(new \DateTime());
        }

        $this->entityManager->persist($libraryBook);
        $this->entityManager->flush();

        return $this->json($libraryBook, 201, [], ['groups' => ['libraryBook:read']]);
    }

    #[Route('/remove/{bookId}', name: 'library_remove', methods: ['DELETE'])]
    public function removeFromLibrary(string $bookId): JsonResponse
    {
        error_log("=== Début de la suppression du livre de la bibliothèque ===");
        error_log("bookId reçu: " . $bookId);

        // Trouver d'abord le livre par son googleBooksId
        error_log("Recherche du livre avec googleBooksId: " . $bookId);
        $book = $this->entityManager->getRepository(Book::class)->findOneBy([
            'googleBooksId' => $bookId
        ]);

        if (!$book) {
            error_log("Erreur: Livre non trouvé avec googleBooksId: " . $bookId);
            return $this->json(['error' => 'Book not found'], 404);
        }
        error_log("Livre trouvé: " . $book->getTitle());

        // Ensuite trouver le LibraryBook associé
        error_log("Recherche du LibraryBook pour l'utilisateur: " . $this->getUser()->getEmail());
        $libraryBook = $this->entityManager->getRepository(LibraryBook::class)->findOneBy([
            'user' => $this->getUser(),
            'book' => $book
        ]);

        if (!$libraryBook) {
            error_log("Erreur: LibraryBook non trouvé pour l'utilisateur");
            return $this->json(['error' => 'Book not found in your library'], 404);
        }
        error_log("LibraryBook trouvé avec l'ID: " . $libraryBook->getId());

        try {
            $this->entityManager->remove($libraryBook);
            $this->entityManager->flush();
            error_log("Suppression réussie dans la base de données");
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression dans la base de données: " . $e->getMessage());
            return $this->json(['error' => 'Database error'], 500);
        }

        error_log("=== Fin de la suppression du livre de la bibliothèque ===");
        return $this->json(['message' => 'Book removed from library']);
    }

    #[Route('/update-status/{bookId}', name: 'library_update_status', methods: ['PATCH'])]
    public function updateStatus(string $bookId, Request $request): JsonResponse
    {
        error_log("=== Début de la mise à jour du statut ===");
        error_log("bookId reçu: " . $bookId);
        error_log("Méthode HTTP: " . $request->getMethod());
        error_log("Headers: " . json_encode($request->headers->all()));
        error_log("Content: " . $request->getContent());
        error_log("Content-Type: " . $request->headers->get('Content-Type'));
        error_log("Accept: " . $request->headers->get('Accept'));
        error_log("Authorization: " . $request->headers->get('Authorization'));
        
        $data = json_decode($request->getContent(), true);
        error_log("Données reçues: " . json_encode($data));
        
        if (!isset($data['status'])) {
            error_log("Erreur: status manquant dans les données");
            return $this->json(['error' => 'status is required'], 400);
        }

        // Trouver directement le LibraryBook par son ID
        error_log("Recherche du LibraryBook avec l'ID: " . $bookId);
        $libraryBook = $this->entityManager->getRepository(LibraryBook::class)->find($bookId);

        if (!$libraryBook) {
            error_log("Erreur: LibraryBook non trouvé avec l'ID: " . $bookId);
            return $this->json(['error' => 'Book not found in your library'], 404);
        }

        // Vérifier que le livre appartient bien à l'utilisateur
        if ($libraryBook->getUser() !== $this->getUser()) {
            error_log("Erreur: Le livre n'appartient pas à l'utilisateur");
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        error_log("LibraryBook trouvé pour le livre: " . $libraryBook->getBook()->getTitle());

        $oldStatus = $libraryBook->getStatus();
        $newStatus = $data['status'];
        error_log("Changement de statut: " . $oldStatus . " -> " . $newStatus);

        $libraryBook->setStatus($newStatus);

        // Gérer les dates en fonction du statut
        if ($newStatus === 'reading' && $oldStatus !== 'reading') {
            error_log("Mise à jour de la date de début de lecture");
            $libraryBook->setStartDate(new \DateTime());
        } elseif ($newStatus === 'read' && $oldStatus !== 'read') {
            error_log("Mise à jour de la date de fin de lecture");
            $libraryBook->setEndDate(new \DateTime());
        }

        try {
            $this->entityManager->flush();
            error_log("Mise à jour réussie dans la base de données");
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour dans la base de données: " . $e->getMessage());
            return $this->json(['error' => 'Database error'], 500);
        }

        error_log("=== Fin de la mise à jour du statut ===");
        return $this->json($libraryBook, 200, [], ['groups' => ['libraryBook:read']]);
    }

    #[Route('/status/{bookId}', name: 'library_get_status', methods: ['GET'])]
    public function getStatus(string $bookId): JsonResponse
    {
        $libraryBook = $this->entityManager->getRepository(LibraryBook::class)->findOneBy([
            'user' => $this->getUser(),
            'book' => $bookId
        ]);

        if (!$libraryBook) {
            return $this->json(['status' => 'not_in_library']);
        }

        return $this->json([
            'status' => $libraryBook->getStatus(),
            'startDate' => $libraryBook->getStartDate(),
            'endDate' => $libraryBook->getEndDate()
        ]);
    }

    #[Route('/book-link/{googleBooksId}', name: 'library_book_link', methods: ['GET'])]
    public function getLibraryBookLink(string $googleBooksId): JsonResponse
    {
        $book = $this->entityManager->getRepository(Book::class)->findOneBy([
            'googleBooksId' => $googleBooksId
        ]);
        if (!$book) {
            return $this->json(['inLibrary' => false, 'libraryBookId' => null]);
        }

        $libraryBook = $this->entityManager->getRepository(LibraryBook::class)->findOneBy([
            'user' => $this->getUser(),
            'book' => $book
        ]);

        if (!$libraryBook) {
            return $this->json(['inLibrary' => false, 'libraryBookId' => null]);
        }

        return $this->json([
            'inLibrary' => true,
            'libraryBookId' => $libraryBook->getId()
        ]);
    }
} 