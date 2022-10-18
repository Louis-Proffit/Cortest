<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221001172718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE epreuve_version ADD epreuve_id INT NOT NULL');
        $this->addSql('ALTER TABLE epreuve_version ADD CONSTRAINT FK_EAFCBB4FAB990336 FOREIGN KEY (epreuve_id) REFERENCES epreuve (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_EAFCBB4FAB990336 ON epreuve_version (epreuve_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE epreuve_version DROP CONSTRAINT FK_EAFCBB4FAB990336');
        $this->addSql('DROP INDEX IDX_EAFCBB4FAB990336');
        $this->addSql('ALTER TABLE epreuve_version DROP epreuve_id');
    }
}
