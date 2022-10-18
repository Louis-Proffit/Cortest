<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221001181033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve_notation_directe (id INT NOT NULL, code_epreuve SMALLINT NOT NULL, version SMALLINT NOT NULL, num_question SMALLINT NOT NULL, rep_a DOUBLE PRECISION NOT NULL, rep_b DOUBLE PRECISION NOT NULL, rep_c DOUBLE PRECISION NOT NULL, rep_d DOUBLE PRECISION NOT NULL, rep_e DOUBLE PRECISION NOT NULL, bool_a BOOLEAN NOT NULL, bool_b BOOLEAN NOT NULL, bool_c BOOLEAN DEFAULT NULL, bool_d BOOLEAN DEFAULT NULL, bool_e BOOLEAN DEFAULT NULL, no_rep DOUBLE PRECISION NOT NULL, intitule VARCHAR(50) NOT NULL, abreviation VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve_version (id INT NOT NULL, epreuve_id INT NOT NULL, version SMALLINT NOT NULL, statut SMALLINT NOT NULL, descriptif VARCHAR(255) DEFAULT NULL, nom SMALLINT NOT NULL, prenom SMALLINT NOT NULL, nom_jeune_fille SMALLINT NOT NULL, niveau_scolaire SMALLINT NOT NULL, naissance SMALLINT NOT NULL, sexe SMALLINT NOT NULL, concours SMALLINT NOT NULL, sgap SMALLINT NOT NULL, date_examen SMALLINT NOT NULL, type_concours SMALLINT NOT NULL, version_batterie SMALLINT NOT NULL, reserve SMALLINT NOT NULL, champ1 SMALLINT NOT NULL, champ2 SMALLINT NOT NULL, code_barre SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_eafcbb4fab990336 ON epreuve_version (epreuve_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve_echelle_simple (id INT NOT NULL, code_epreuve SMALLINT NOT NULL, version SMALLINT NOT NULL, num_echelle SMALLINT NOT NULL, type_comptabilisation SMALLINT NOT NULL, question_associee SMALLINT NOT NULL, info_complementaire SMALLINT DEFAULT NULL, intitule VARCHAR(50) NOT NULL, abreviation VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve (id INT NOT NULL, code SMALLINT NOT NULL, etiquette VARCHAR(255) DEFAULT NULL, descriptif VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_notation_directe');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_version');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_echelle_simple');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve');
    }
}
