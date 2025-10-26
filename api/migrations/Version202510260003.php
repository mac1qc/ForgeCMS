<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use ForgeCMS\Users\Entity\User;

final class Version202510260003 extends AbstractMigration
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
    }

    public function getDescription(): string
    {
        return 'Add default admin user';
    }

    public function up(Schema $schema): void
    {
        $user = new User();
        $user->setName('admin');
        $user->setEmail('admin@example.com');
        $user->setAdminLevel(1);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $this->addSql('INSERT INTO users (name, email, password, admin_level, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)', [
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getAdminLevel(),
            $user->getCreatedAt()->format('Y-m-d H:i:s'),
            $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM users WHERE email = ?', ['admin@example.com']);
    }
}
