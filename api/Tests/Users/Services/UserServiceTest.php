<?php

namespace ForgeCMS\Tests\Users\Services;

use ForgeCMS\Users\Entity\User;
use ForgeCMS\Users\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends KernelTestCase
{
    public function testLogin(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $userService = $container->get(UserService::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail('admin@example.com');
        $hashedPassword = $passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);
        $user->setAdminLevel(1);
        $user->setName('admin');
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->persist($user);
        $entityManager->flush();

        $token = $userService->login('admin@example.com', 'password');

        $this->assertIsString($token);
    }
}
