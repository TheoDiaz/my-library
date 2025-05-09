<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiDocController extends AbstractController
{
    #[Route('/api/docs', name: 'app_api_docs', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('api_doc/index.html.twig', [
            'title' => 'Documentation API',
        ]);
    }

    #[Route('/api/docs.json', name: 'app_api_docs_json', methods: ['GET'])]
    public function getApiDocs(): JsonResponse
    {
        $docs = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Ma Bibliothèque API',
                'version' => '1.0.0',
                'description' => 'API pour gérer une bibliothèque personnelle',
            ],
            'paths' => [
                '/api/library_books' => [
                    'get' => [
                        'summary' => 'Récupère la liste des livres de la bibliothèque',
                        'responses' => [
                            '200' => [
                                'description' => 'Liste des livres récupérée avec succès',
                            ],
                        ],
                    ],
                    'post' => [
                        'summary' => 'Ajoute un nouveau livre à la bibliothèque',
                        'responses' => [
                            '201' => [
                                'description' => 'Livre ajouté avec succès',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->json($docs);
    }
} 