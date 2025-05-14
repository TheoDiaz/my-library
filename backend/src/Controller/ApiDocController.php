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
                '/api/openlibrary/search' => [
                    'get' => [
                        'summary' => 'Recherche des livres sur Open Library',
                        'parameters' => [
                            [
                                'name' => 'q',
                                'in' => 'query',
                                'required' => true,
                                'schema' => [
                                    'type' => 'string'
                                ],
                                'description' => 'Terme de recherche'
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Résultats de la recherche',
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
                                                    'isbn' => ['type' => 'string']
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '400' => [
                                'description' => 'Paramètre de recherche manquant'
                            ]
                        ]
                    ]
                ],
                '/api/openlibrary/details/{id}' => [
                    'get' => [
                        'summary' => 'Récupère les détails d\'un livre par son ID Open Library',
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => [
                                    'type' => 'string'
                                ],
                                'description' => 'ID du livre sur Open Library'
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Détails du livre',
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
                '/api/openlibrary/isbn/{isbn}' => [
                    'get' => [
                        'summary' => 'Recherche un livre par son ISBN',
                        'parameters' => [
                            [
                                'name' => 'isbn',
                                'in' => 'path',
                                'required' => true,
                                'schema' => [
                                    'type' => 'string'
                                ],
                                'description' => 'ISBN du livre'
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Détails du livre',
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
                                                    'isbn' => ['type' => 'string']
                                                ]
                                            ]
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
} 