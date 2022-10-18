<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221001132059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE epreuve_notation_directe ALTER bool_c DROP NOT NULL');
        $this->addSql('ALTER TABLE epreuve_notation_directe ALTER bool_d DROP NOT NULL');
        $this->addSql('ALTER TABLE epreuve_notation_directe ALTER bool_e DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE epreuve_notation_directe ALTER bool_c SET NOT NULL');
        $this->addSql('ALTER TABLE epreuve_notation_directe ALTER bool_d SET NOT NULL');
        $this->addSql('ALTER TABLE epreuve_notation_directe ALTER bool_e SET NOT NULL');
    }
}
