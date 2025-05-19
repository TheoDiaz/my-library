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
                '/api/register' => [
                    'post' => [
                        'summary' => 'Inscription d\'un nouvel utilisateur',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['email', 'password'],
                                        'properties' => [
                                            'email' => [
                                                'type' => 'string',
                                                'format' => 'email',
                                                'description' => 'Email de l\'utilisateur'
                                            ],
                                            'password' => [
                                                'type' => 'string',
                                                'format' => 'password',
                                                'description' => 'Mot de passe de l\'utilisateur'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'Utilisateur créé avec succès',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'message' => ['type' => 'string'],
                                                'user' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'id' => ['type' => 'integer'],
                                                        'email' => ['type' => 'string']
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '400' => [
                                'description' => 'Erreur de validation',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'message' => ['type' => 'string'],
                                                'errors' => [
                                                    'type' => 'array',
                                                    'items' => ['type' => 'string']
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                '/api/login_check' => [
                    'post' => [
                        'summary' => 'Connexion utilisateur',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['email', 'password'],
                                        'properties' => [
                                            'email' => [
                                                'type' => 'string',
                                                'format' => 'email'
                                            ],
                                            'password' => [
                                                'type' => 'string',
                                                'format' => 'password'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Connexion réussie',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'token' => ['type' => 'string']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '401' => [
                                'description' => 'Identifiants invalides'
                            ]
                        ]
                    ]
                ],
                '/api/googlebooks/search' => [
                    'get' => [
                        'summary' => 'Recherche de livres via Google Books',
                        'parameters' => [
                            [
                                'name' => 'q',
                                'in' => 'query',
                                'required' => true,
                                'schema' => [
                                    'type' => 'string'
                                ],
                                'description' => 'Terme de recherche'
                            ],
                            [
                                'name' => 'lang',
                                'in' => 'query',
                                'required' => false,
                                'schema' => [
                                    'type' => 'string'
                                ],
                                'description' => 'Code de langue (ex: fr)'
                            ],
                            [
                                'name' => 'maxResults',
                                'in' => 'query',
                                'required' => false,
                                'schema' => [
                                    'type' => 'integer'
                                ],
                                'description' => 'Nombre maximum de résultats (défaut: 20)'
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Liste des livres correspondant à la recherche',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'title' => ['type' => 'string'],
                                                    'author_name' => ['type' => 'string'],
                                                    'first_publish_year' => ['type' => 'integer'],
                                                    'cover_i' => ['type' => 'integer'],
                                                    'isbn' => ['type' => 'string'],
                                                    'edition_key' => ['type' => 'string'],
                                                    'key' => ['type' => 'string']
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '400' => [
                                'description' => 'Paramètres de recherche manquants'
                            ]
                        ]
                    ]
                ],
                '/api/googlebooks/details/{id}' => [
                    'get' => [
                        'summary' => 'Récupère les détails d\'un livre via son ID Google Books',
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => [
                                    'type' => 'string'
                                ],
                                'description' => 'ID du livre Google Books'
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Détails complets du livre',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object'
                                        ]
                                    ]
                                ]
                            ],
                            '404' => [
                                'description' => 'Livre non trouvé'
                            ]
                        ]
                    ]
                ],
                '/api/books' => [
                    'get' => [
                        'summary' => 'Récupère la liste des livres de la bibliothèque',
                        'security' => [['bearerAuth' => []]],
                        'responses' => [
                            '200' => [
                                'description' => 'Liste des livres récupérée avec succès',
                            ],
                        ],
                    ],
                    'post' => [
                        'summary' => 'Ajoute un nouveau livre à la bibliothèque',
                        'security' => [['bearerAuth' => []]],
                        'responses' => [
                            '201' => [
                                'description' => 'Livre ajouté avec succès',
                            ],
                        ],
                    ],
                ],
                '/api/books/{id}' => [
                    'get' => [
                        'summary' => 'Récupère les détails d\'un livre de la bibliothèque',
                        'security' => [['bearerAuth' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => [
                                    'type' => 'integer'
                                ],
                                'description' => 'ID du livre dans la bibliothèque'
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Détails du livre',
                            ],
                            '404' => [
                                'description' => 'Livre non trouvé'
                            ]
                        ]
                    ],
                    'delete' => [
                        'summary' => 'Supprime un livre de la bibliothèque',
                        'security' => [['bearerAuth' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => [
                                    'type' => 'integer'
                                ],
                                'description' => 'ID du livre à supprimer'
                            ]
                        ],
                        'responses' => [
                            '204' => [
                                'description' => 'Livre supprimé avec succès',
                            ],
                            '404' => [
                                'description' => 'Livre non trouvé'
                            ]
                        ]
                    ]
                ]
            ],
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT'
                    ]
                ]
            ]
        ];

        return $this->json($docs);
    }

    private function getApiEndpoints(): array
    {
        return [
            '/api/googlebooks/search' => [
                'method' => 'GET',
                'description' => 'Recherche de livres via Google Books',
                'parameters' => [
                    'q' => 'Terme de recherche',
                    'lang' => 'Code de langue (ex: fr)',
                    'maxResults' => 'Nombre maximum de résultats (défaut: 20)'
                ],
                'response' => 'Liste des livres correspondant à la recherche'
            ],
            '/api/googlebooks/details/{id}' => [
                'method' => 'GET',
                'description' => 'Récupère les détails d\'un livre via son ID Google Books',
                'parameters' => [
                    'id' => 'ID du livre Google Books'
                ],
                'response' => 'Détails complets du livre'
            ]
        ];
    }
} 