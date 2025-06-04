<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240320000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ status à la table library_book';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE library_book ADD status VARCHAR(20) NOT NULL DEFAULT \'to_read\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE library_book DROP status');
    }
} 