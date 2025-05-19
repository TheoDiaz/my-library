<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\LibraryBook;
use App\Service\GoogleBooksService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LibraryBookController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GoogleBooksService $googleBooksService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('/api/librarybooks/add', name: 'librarybook_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): JsonResponse
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
        
        // Optionnel : ajouter les dates et notes si fournies
        if (isset($data['startDate'])) {
            $libraryBook->setStartDate(new \DateTime($data['startDate']));
        }
        if (isset($data['endDate'])) {
            $libraryBook->setEndDate(new \DateTime($data['endDate']));
        }
        if (isset($data['rating'])) {
            $libraryBook->setRating($data['rating']);
        }
        if (isset($data['comments'])) {
            $libraryBook->setComments($data['comments']);
        }

        $this->entityManager->persist($libraryBook);
        $this->entityManager->flush();

        return $this->json($libraryBook, 201, [], ['groups' => ['libraryBook:read']]);
    }

    #[Route('/api/librarybooks', name: 'librarybook_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(): JsonResponse
    {
        $user = $this->getUser();
        error_log("Récupération des livres pour l'utilisateur: " . $user->getEmail());
        
        $libraryBooks = $this->entityManager->getRepository(LibraryBook::class)->findBy(
            ['user' => $user],
            ['id' => 'DESC']
        );
        
        error_log("Nombre de livres trouvés: " . count($libraryBooks));
        
        $response = $this->json(['member' => $libraryBooks], 200, [], ['groups' => ['libraryBook:read']]);
        error_log("Réponse JSON: " . $response->getContent());
        
        return $response;
    }
} 