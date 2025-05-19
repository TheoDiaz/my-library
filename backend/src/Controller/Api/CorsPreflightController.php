<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CorsPreflightController
{
    #[Route('/api/{any}', name: 'api_cors_preflight', methods: ['OPTIONS'], requirements: ['any' => '.+'])]
    public function preflight(): Response
    {
        return new Response();
    }

    #[Route('/api/login_check', name: 'api_login_check_options', methods: ['OPTIONS'])]
    public function loginCheckOptions(): Response
    {
        return new Response();
    }

    #[Route('/api/test-cors', name: 'api_test_cors_options', methods: ['OPTIONS'])]
    public function testCorsOptions(): Response
    {
        return new Response();
    }

    #[Route('/api/librarybooks', name: 'api_librarybooks_options', methods: ['OPTIONS'])]
    public function libraryBooksOptions(): Response
    {
        return new Response();
    }
} 