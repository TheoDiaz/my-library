<?php

namespace App\Controller;

use App\Service\GoogleBooksService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    public function __construct(private readonly GoogleBooksService $googleBooksService)
    {
    }

    #[Route('/test/search/{query}', name: 'test_search')]
    public function testSearch(string $query): JsonResponse
    {
        $results = $this->googleBooksService->searchBooks($query, 'fr', 20);
        return $this->json($results);
    }

    #[Route('/test/details/{id}', name: 'test_details')]
    public function testDetails(string $id): JsonResponse
    {
        $details = $this->googleBooksService->getBookDetails($id);
        return $this->json($details);
    }
} 