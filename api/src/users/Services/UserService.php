<?php

declare(strict_types=1);

namespace ForgeCMS\Users\Services;

use ForgeCMS\Users\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserService
{
    private $entityManager;
    private $passwordHasher;
    private $params;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ParameterBagInterface $params)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->params = $params;
    }

    public function login(string $email, string $password): ?string
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return null;
        }

        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->params->get('env(JWT_SECRET_KEY)'))
        );

        $now = new \DateTimeImmutable();
        $token = $config->builder()
            ->issuedBy($this->params->get('env(DEFAULT_URI)'))
            ->permittedFor($this->params->get('env(DEFAULT_URI)'))
            ->identifiedBy('4f1g23a12aa')
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', $user->getId())
            ->getToken($config->signer(), $config->signingKey());

        return $token->toString();
    }
}
