<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250517154613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la colonne google_books_id à la table book';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book ADD google_books_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CBE5A331A2B1A9A6 ON book (google_books_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_CBE5A331A2B1A9A6 ON book');
        $this->addSql('ALTER TABLE book DROP google_books_id');
    }
}
