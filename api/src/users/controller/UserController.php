<?php

namespace ForgeCMS\Users\Controller;

use ForgeCMS\Users\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/api/users/login', name: 'api_users_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        $token = $this->userService->login($email, $password);

        if ($token === null) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        return new JsonResponse(['status' => 'ok', 'token' => $token]);
    }
}
