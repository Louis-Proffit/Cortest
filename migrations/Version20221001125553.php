<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221001125553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE epreuve_echelle_simple ADD intitule VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE epreuve_echelle_simple ADD abreviation VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE epreuve_notation_directe ADD intitule VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE epreuve_notation_directe ADD abreviation VARCHAR(10) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE epreuve_notation_directe DROP intitule');
        $this->addSql('ALTER TABLE epreuve_notation_directe DROP abreviation');
        $this->addSql('ALTER TABLE epreuve_echelle_simple DROP intitule');
        $this->addSql('ALTER TABLE epreuve_echelle_simple DROP abreviation');
    }
}
