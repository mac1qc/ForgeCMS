<?php

namespace ForgeCMS\Users\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/users/login', name: 'api_users_login', methods: ['GET'])]
    public function login(Request $request): JsonResponse
    {
        $email = $request->query->get('email');
        $password = $request->query->get('password');

        // TODO: Implement actual login logic
        if ($email === 'admin@example.com' && $password === 'password') {
            return new JsonResponse(['status' => 'ok']);
        }

        return new JsonResponse(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }
}
