<?php

namespace App\Controller\Api;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        // Générer le JWT pour l'utilisateur connecté
        $token = $JWTManager->create($user);

        // Retourner le token dans la réponse JSON
        return new JsonResponse(['token' => $token]);
    }
} 