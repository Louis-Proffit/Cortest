<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221002143925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE epreuve_notation_directe ADD version_id INT NOT NULL');
        $this->addSql('ALTER TABLE epreuve_notation_directe DROP code_epreuve');
        $this->addSql('ALTER TABLE epreuve_notation_directe DROP version');
        $this->addSql('ALTER TABLE epreuve_notation_directe ALTER no_rep DROP NOT NULL');
        $this->addSql('ALTER TABLE epreuve_notation_directe ADD CONSTRAINT FK_C888D6B54BBC2705 FOREIGN KEY (version_id) REFERENCES epreuve_version (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_C888D6B54BBC2705 ON epreuve_notation_directe (version_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE epreuve_notation_directe DROP CONSTRAINT FK_C888D6B54BBC2705');
        $this->addSql('DROP INDEX IDX_C888D6B54BBC2705');
        $this->addSql('ALTER TABLE epreuve_notation_directe ADD code_epreuve SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE epreuve_notation_directe ADD version SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE epreuve_notation_directe DROP version_id');
        $this->addSql('ALTER TABLE epreuve_notation_directe ALTER no_rep SET NOT NULL');
    }
}
