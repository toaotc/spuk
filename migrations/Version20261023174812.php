<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261023174812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial database.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment (id BLOB NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, active BOOLEAN DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE appointment_kid (appointment_id BLOB NOT NULL, kid_id INTEGER NOT NULL, PRIMARY KEY (appointment_id, kid_id), CONSTRAINT FK_EA731F64E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_EA731F646A973770 FOREIGN KEY (kid_id) REFERENCES kid (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EA731F64E5B533F9 ON appointment_kid (appointment_id)');
        $this->addSql('CREATE INDEX IDX_EA731F646A973770 ON appointment_kid (kid_id)');
        $this->addSql('CREATE TABLE guardian (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, uuid BLOB NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE guardian_kid (guardian_id INTEGER NOT NULL, kid_id INTEGER NOT NULL, PRIMARY KEY (guardian_id, kid_id), CONSTRAINT FK_CE33C57A11CC8B0A FOREIGN KEY (guardian_id) REFERENCES guardian (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CE33C57A6A973770 FOREIGN KEY (kid_id) REFERENCES kid (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_CE33C57A11CC8B0A ON guardian_kid (guardian_id)');
        $this->addSql('CREATE INDEX IDX_CE33C57A6A973770 ON guardian_kid (kid_id)');
        $this->addSql('CREATE TABLE kid (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, kid_group_id INTEGER DEFAULT NULL, CONSTRAINT FK_4523887CE71D8A57 FOREIGN KEY (kid_group_id) REFERENCES kid_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4523887CE71D8A57 ON kid (kid_group_id)');
        $this->addSql('CREATE TABLE kid_group (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE appointment_kid');
        $this->addSql('DROP TABLE guardian');
        $this->addSql('DROP TABLE guardian_kid');
        $this->addSql('DROP TABLE kid');
        $this->addSql('DROP TABLE kid_group');
        $this->addSql('DROP TABLE user');
    }
}
