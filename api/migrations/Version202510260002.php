<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202510260002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates initial tables for users, sites, pages, and content.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, email VARCHAR(128) NOT NULL, password VARCHAR(128) NOT NULL, admin_level INT NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX user_email ON users (email)');
        $this->addSql('CREATE INDEX login_check ON users (deleted_at, email, password)');

        $this->addSql('CREATE TABLE sites (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(64) NOT NULL, base_url VARCHAR(128) NOT NULL, description VARCHAR(256) DEFAULT NULL, contact_email VARCHAR(128) DEFAULT NULL, logo VARCHAR(256) DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX sites_to_user (user_id, deleted_at), INDEX active_site (deleted_at), PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE sites ADD CONSTRAINT FK_D32655E6A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');

        $this->addSql('CREATE TABLE pages (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, name VARCHAR(128) NOT NULL, description VARCHAR(256) DEFAULT NULL, slug VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX pages_to_site (site_id, deleted_at), PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575F6BD1646 FOREIGN KEY (site_id) REFERENCES sites (id)');

        $this->addSql('CREATE TABLE page_contents (id INT AUTO_INCREMENT NOT NULL, page_id INT NOT NULL, content_type_id INT NOT NULL, content TEXT NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX contents_to_page (page_id, deleted_at), PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE page_contents ADD CONSTRAINT FK_5A725051C4663E4 FOREIGN KEY (page_id) REFERENCES pages (id)');

        $this->addSql('CREATE TABLE content_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, layout VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE page_contents ADD CONSTRAINT FK_5A72505149A6F42E FOREIGN KEY (content_type_id) REFERENCES content_types (id)');

        $this->addSql('CREATE TABLE sites_menu (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, menu JSON DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX menu_to_site (site_id, deleted_at), PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE sites_menu ADD CONSTRAINT FK_5999D247F6BD1646 FOREIGN KEY (site_id) REFERENCES sites (id)');

        $this->addSql('CREATE TABLE content_types_per_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, content_type_id INT DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, INDEX cotent_type_to_user (user_id, content_type_id), PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE content_types_per_user ADD CONSTRAINT FK_3A47A446A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE content_types_per_user ADD CONSTRAINT FK_3A47A44649A6F42E FOREIGN KEY (content_type_id) REFERENCES content_types (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sites DROP FOREIGN KEY FK_D32655E6A76ED395');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575F6BD1646');
        $this->addSql('ALTER TABLE page_contents DROP FOREIGN KEY FK_5A725051C4663E4');
        $this->addSql('ALTER TABLE page_contents DROP FOREIGN KEY FK_5A72505149A6F42E');
        $this->addSql('ALTER TABLE sites_menu DROP FOREIGN KEY FK_5999D247F6BD1646');
        $this->addSql('ALTER TABLE content_types_per_user DROP FOREIGN KEY FK_3A47A446A76ED395');
        $this->addSql('ALTER TABLE content_types_per_user DROP FOREIGN KEY FK_3A47A44649A6F42E');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE page_contents');
        $this->addSql('DROP TABLE content_types');
        $this->addSql('DROP TABLE sites_menu');
        $this->addSql('DROP TABLE content_types_per_user');
    }
}
