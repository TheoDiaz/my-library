<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250517160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renomme la colonne cover_id en cover dans la table book';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book CHANGE cover_id cover VARCHAR(255) DEFAULT NULL');
    }
    
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book CHANGE cover cover_id VARCHAR(255) DEFAULT NULL');
    }
} 