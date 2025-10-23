<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/api/test', name: 'api_test')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new API!',
            'path' => 'src/Controller/TestController.php',
        ]);
    }
}
