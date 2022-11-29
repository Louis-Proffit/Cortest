<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221122013251 extends AbstractMigration
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

        $this->addSql('CREATE TABLE epreuve_echelle_epreuve_echelle (epreuve_echelle_source INT NOT NULL, epreuve_echelle_target INT NOT NULL, PRIMARY KEY(epreuve_echelle_source, epreuve_echelle_target))');
        $this->addSql('CREATE INDEX idx_53e649b8d18014f1 ON epreuve_echelle_epreuve_echelle (epreuve_echelle_target)');
        $this->addSql('CREATE INDEX idx_53e649b8c865447e ON epreuve_echelle_epreuve_echelle (epreuve_echelle_source)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve_classe_critere (id INT NOT NULL, caracteristique SMALLINT NOT NULL, relation SMALLINT NOT NULL, valeur VARCHAR(255) NOT NULL, valeur_sup VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve_classe (id INT NOT NULL, echelle_id INT NOT NULL, limite DOUBLE PRECISION NOT NULL, valeur_droite SMALLINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_6ac91c21dd268c11 ON epreuve_classe (echelle_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve_version (id INT NOT NULL, epreuve_id INT NOT NULL, code_version SMALLINT NOT NULL, statut SMALLINT NOT NULL, descriptif VARCHAR(255) DEFAULT NULL, nom SMALLINT NOT NULL, prenom SMALLINT NOT NULL, nom_jeune_fille SMALLINT NOT NULL, niveau_scolaire SMALLINT NOT NULL, naissance SMALLINT NOT NULL, sexe SMALLINT NOT NULL, concours SMALLINT NOT NULL, sgap SMALLINT NOT NULL, date_examen SMALLINT NOT NULL, type_concours SMALLINT NOT NULL, version_batterie SMALLINT NOT NULL, reserve SMALLINT NOT NULL, champ1 SMALLINT NOT NULL, champ2 SMALLINT NOT NULL, code_barre SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_eafcbb4fab990336 ON epreuve_version (epreuve_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve (id INT NOT NULL, code SMALLINT NOT NULL, etiquette VARCHAR(255) DEFAULT NULL, descriptif VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve_echelle (id INT NOT NULL, version_id INT NOT NULL, num_echelle SMALLINT NOT NULL, type_comptabilisation SMALLINT NOT NULL, info_complementaire VARCHAR(50) DEFAULT NULL, intitule VARCHAR(50) NOT NULL, abreviation VARCHAR(10) NOT NULL, type SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8f8511c14bbc2705 ON epreuve_echelle (version_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve_echelle_epreuve_notation_directe (epreuve_echelle_id INT NOT NULL, epreuve_notation_directe_id INT NOT NULL, PRIMARY KEY(epreuve_echelle_id, epreuve_notation_directe_id))');
        $this->addSql('CREATE INDEX idx_cde9c16491d59088 ON epreuve_echelle_epreuve_notation_directe (epreuve_notation_directe_id)');
        $this->addSql('CREATE INDEX idx_cde9c16438276d77 ON epreuve_echelle_epreuve_notation_directe (epreuve_echelle_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve_notation_directe (id INT NOT NULL, version_id INT NOT NULL, num_question SMALLINT NOT NULL, rep_a DOUBLE PRECISION NOT NULL, rep_b DOUBLE PRECISION NOT NULL, rep_c DOUBLE PRECISION NOT NULL, rep_d DOUBLE PRECISION NOT NULL, rep_e DOUBLE PRECISION NOT NULL, bool_a BOOLEAN NOT NULL, bool_b BOOLEAN NOT NULL, bool_c BOOLEAN DEFAULT NULL, bool_d BOOLEAN DEFAULT NULL, bool_e BOOLEAN DEFAULT NULL, no_rep DOUBLE PRECISION DEFAULT NULL, intitule VARCHAR(50) NOT NULL, abreviation VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_c888d6b54bbc2705 ON epreuve_notation_directe (version_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('CREATE TABLE epreuve_classe_epreuve_classe_critere (epreuve_classe_id INT NOT NULL, epreuve_classe_critere_id INT NOT NULL, PRIMARY KEY(epreuve_classe_id, epreuve_classe_critere_id))');
        $this->addSql('CREATE INDEX idx_23c1708d2db8dc2 ON epreuve_classe_epreuve_classe_critere (epreuve_classe_critere_id)');
        $this->addSql('CREATE INDEX idx_23c17085b01cec4 ON epreuve_classe_epreuve_classe_critere (epreuve_classe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_echelle_epreuve_echelle');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_classe_critere');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_classe');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_version');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_echelle');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_echelle_epreuve_notation_directe');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_notation_directe');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQL100Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL100Platform'."
        );

        $this->addSql('DROP TABLE epreuve_classe_epreuve_classe_critere');
    }
}
