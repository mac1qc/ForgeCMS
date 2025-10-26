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
        $this->addSql('
            CREATE TABLE USERS (
                ID INT AUTO_INCREMENT NOT NULL,
                NAME VARCHAR(64) NOT NULL,
                EMAIL VARCHAR(128) NOT NULL,
                PASSWORD VARCHAR(128) NOT NULL,
                ADMIN_LEVEL INT NOT NULL,
                CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                DELETED_AT DATETIME DEFAULT NULL,
                PRIMARY KEY(ID),
                INDEX USER_EMAIL (EMAIL),
                INDEX LOGIN_CHECK (DELETED_AT, EMAIL, PASSWORD)
            )
        ');

        $this->addSql('
            CREATE TABLE SITES (
                ID INT AUTO_INCREMENT NOT NULL,
                USER_ID INT NOT NULL,
                NAME VARCHAR(64) NOT NULL,
                BASE_URL VARCHAR(128) NOT NULL,
                DESCRIPTION VARCHAR(256) DEFAULT NULL,
                CONTACT_EMAIL VARCHAR(128) DEFAULT NULL,
                LOGO VARCHAR(256) DEFAULT NULL,
                CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                DELETED_AT DATETIME DEFAULT NULL,
                PRIMARY KEY(ID),
                INDEX SITES_TO_USER (USER_ID, DELETED_AT),
                INDEX ACTIVE_SITE (DELETED_AT)
            )
        ');

        $this->addSql('
            CREATE TABLE PAGES (
                ID INT AUTO_INCREMENT NOT NULL,
                SITE_ID INT NOT NULL,
                NAME VARCHAR(128) NOT NULL,
                DESCRIPTION VARCHAR(256) DEFAULT NULL,
                SLUG VARCHAR(128) NOT NULL,
                CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                DELETED_AT DATETIME DEFAULT NULL,
                PRIMARY KEY(ID),
                INDEX PAGES_TO_SITE (SITE_ID, DELETED_AT)
            )
        ');

        $this->addSql('
            CREATE TABLE PAGE_CONTENTS (
                ID INT AUTO_INCREMENT NOT NULL,
                PAGE_ID INT NOT NULL,
                CONTENT_TYPE_ID INT NOT NULL,
                CONTENT TEXT NOT NULL,
                CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                DELETED_AT DATETIME DEFAULT NULL,
                PRIMARY KEY(ID),
                INDEX CONTENTS_TO_PAGE (PAGE_ID, DELETED_AT)
            )
        ');

        $this->addSql('
            CREATE TABLE CONTENT_TYPES (
                ID INT AUTO_INCREMENT NOT NULL,
                NAME VARCHAR(64) NOT NULL,
                LAYOUT VARCHAR(64) NOT NULL,
                CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                DELETED_AT DATETIME DEFAULT NULL,
                PRIMARY KEY(ID)
            )
        ');

        $this->addSql('
            CREATE TABLE SITES_MENU (
                ID INT AUTO_INCREMENT NOT NULL,
                SITE_ID INT DEFAULT NULL,
                MENU JSON DEFAULT NULL,
                CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                DELETED_AT DATETIME DEFAULT NULL,
                PRIMARY KEY(ID),
                INDEX MENU_TO_SITE (SITE_ID, DELETED_AT)
            )
        ');

        $this->addSql('
            CREATE TABLE CONTENT_TYPES_PER_USER (
                ID INT AUTO_INCREMENT NOT NULL,
                USER_ID INT DEFAULT NULL,
                CONTENT_TYPE_ID INT DEFAULT NULL,
                CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                DELETED_AT DATETIME DEFAULT NULL,
                PRIMARY KEY(ID),
                INDEX COTENT_TYPE_TO_USER (USER_ID, CONTENT_TYPE_ID)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE USERS');
        $this->addSql('DROP TABLE SITES');
        $this->addSql('DROP TABLE PAGES');
        $this->addSql('DROP TABLE PAGE_CONTENTS');
        $this->addSql('DROP TABLE CONTENT_TYPES');
        $this->addSql('DROP TABLE SITES_MENU');
        $this->addSql('DROP TABLE CONTENT_TYPES_PER_USER');
    }
}
