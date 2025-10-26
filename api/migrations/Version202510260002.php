<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202510260002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the initial database schema.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(64) NOT NULL, email VARCHAR(128) NOT NULL, password VARCHAR(128) NOT NULL, admin_level INTEGER NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME NULL)');
        $this->addSql('CREATE INDEX user_email ON users (email)');
        $this->addSql('CREATE INDEX login_check ON users (deleted_at, email, password)');

        $this->addSql('CREATE TABLE sites (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, name VARCHAR(64) NOT NULL, base_url VARCHAR(128) NOT NULL, description VARCHAR(256) NULL, contact_email VARCHAR(128) NULL, logo VARCHAR(256) NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME NULL)');
        $this->addSql('CREATE INDEX sites_to_user ON sites (user_id, deleted_at)');
        $this->addSql('CREATE INDEX active_site ON sites (deleted_at)');

        $this->addSql('CREATE TABLE pages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, site_id INTEGER NOT NULL, name VARCHAR(128) NOT NULL, description VARCHAR(256) NULL, slug VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME NULL)');
        $this->addSql('CREATE INDEX pages_to_site ON pages (site_id, deleted_at)');

        $this->addSql('CREATE TABLE page_contents (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, page_id INTEGER NOT NULL, content_type_id INTEGER NOT NULL, content TEXT NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME NULL)');
        $this->addSql('CREATE INDEX contents_to_page ON page_contents (page_id, deleted_at)');

        $this->addSql('CREATE TABLE content_types (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(64) NOT NULL, layout VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME NULL)');

        $this->addSql('CREATE TABLE sites_menu (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, site_id INTEGER NULL, menu JSON NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME NULL)');
        $this->addSql('CREATE INDEX menu_to_site ON sites_menu (site_id, deleted_at)');

        $this->addSql('CREATE TABLE content_types_per_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NULL, content_type_id INTEGER NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME NULL)');
        $this->addSql('CREATE INDEX cotent_type_to_user ON content_types_per_user (user_id, content_type_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE page_contents');
        $this->addSql('DROP TABLE content_types');
        $this->addSql('DROP TABLE sites_menu');
        $this->addSql('DROP TABLE content_types_per_user');
    }
}
