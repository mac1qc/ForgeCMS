<?php

namespace ForgeCMS\Users\Controller;

use ForgeCMS\Users\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/api/users/login', name: 'api_users_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->getParameter('env(JWT_SECRET_KEY)'))
        );

        $now = new \DateTimeImmutable();
        $token = $config->builder()
            ->issuedBy($this->getParameter('env(DEFAULT_URI)'))
            ->permittedFor($this->getParameter('env(DEFAULT_URI)'))
            ->identifiedBy('4f1g23a12aa')
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', $user->getId())
            ->getToken($config->signer(), $config->signingKey());

        return new JsonResponse(['status' => 'ok', 'token' => $token->toString()]);
    }
}
