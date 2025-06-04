<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Wishlist;
use App\Service\GoogleBooksService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/wishlist')]
#[IsGranted('ROLE_USER')]
class WishlistController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GoogleBooksService $googleBooksService
    ) {}

    #[Route('/add', name: 'wishlist_add', methods: ['POST'])]
    public function addToWishlist(Request $request): JsonResponse
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

        // Vérifier si l'utilisateur a déjà ce livre dans sa wishlist
        $existingWishlist = $this->entityManager->getRepository(Wishlist::class)->findOneBy([
            'user' => $this->getUser(),
            'book' => $existingBook
        ]);

        if ($existingWishlist) {
            return $this->json(['error' => 'Book already in your wishlist'], 400);
        }

        // Créer le lien dans la wishlist
        $wishlist = new Wishlist();
        $wishlist->setUser($this->getUser());
        $wishlist->setBook($existingBook);

        $this->entityManager->persist($wishlist);
        $this->entityManager->flush();

        return $this->json($wishlist, 201, [], ['groups' => ['wishlist:read']]);
    }

    #[Route('/remove/{bookId}', name: 'wishlist_remove', methods: ['DELETE'])]
    public function removeFromWishlist(string $bookId): JsonResponse
    {
        error_log("=== Début de la suppression du livre de la wishlist ===");
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

        // Ensuite trouver le Wishlist associé
        error_log("Recherche du Wishlist pour l'utilisateur: " . $this->getUser()->getEmail());
        $wishlist = $this->entityManager->getRepository(Wishlist::class)->findOneBy([
            'user' => $this->getUser(),
            'book' => $book
        ]);

        if (!$wishlist) {
            error_log("Erreur: Wishlist non trouvé pour l'utilisateur");
            return $this->json(['error' => 'Book not found in your wishlist'], 404);
        }
        error_log("Wishlist trouvé avec l'ID: " . $wishlist->getId());

        try {
            $this->entityManager->remove($wishlist);
            $this->entityManager->flush();
            error_log("Suppression réussie dans la base de données");
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression dans la base de données: " . $e->getMessage());
            return $this->json(['error' => 'Database error'], 500);
        }

        error_log("=== Fin de la suppression du livre de la wishlist ===");
        return $this->json(['message' => 'Book removed from wishlist']);
    }

    #[Route('/check/{bookId}', name: 'wishlist_check', methods: ['GET'])]
    public function checkWishlist(string $bookId): JsonResponse
    {
        $wishlist = $this->entityManager->getRepository(Wishlist::class)->findOneBy([
            'user' => $this->getUser(),
            'book' => $bookId
        ]);

        return $this->json(['in_wishlist' => $wishlist !== null]);
    }

    #[Route('', name: 'wishlist_list', methods: ['GET'])]
    public function getWishlist(): JsonResponse
    {
        $wishlists = $this->entityManager->getRepository(Wishlist::class)
            ->findBy(['user' => $this->getUser()]);

        return $this->json(['member' => $wishlists], 200, [], ['groups' => ['wishlist:read']]);
    }
} 