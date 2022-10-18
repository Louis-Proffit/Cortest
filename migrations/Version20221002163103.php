<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221002163103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE epreuve_echelle_simple_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE epreuve_classe_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE epreuve_classe_critere_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE epreuve_echelle_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE epreuve_classe (id INT NOT NULL, echelle_id INT NOT NULL, limite DOUBLE PRECISION NOT NULL, valeur_droite DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6AC91C21DD268C11 ON epreuve_classe (echelle_id)');
        $this->addSql('CREATE TABLE epreuve_classe_critere (id INT NOT NULL, caracteristique SMALLINT NOT NULL, relation SMALLINT NOT NULL, valeur VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE epreuve_echelle (id INT NOT NULL, version_id INT NOT NULL, num_echelle SMALLINT NOT NULL, type_comptabilisation SMALLINT NOT NULL, question_associee SMALLINT NOT NULL, info_complementaire SMALLINT DEFAULT NULL, intitule VARCHAR(50) NOT NULL, abreviation VARCHAR(10) NOT NULL, type SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F8511C14BBC2705 ON epreuve_echelle (version_id)');
        $this->addSql('CREATE TABLE epreuve_echelle_epreuve_notation_directe (epreuve_echelle_id INT NOT NULL, epreuve_notation_directe_id INT NOT NULL, PRIMARY KEY(epreuve_echelle_id, epreuve_notation_directe_id))');
        $this->addSql('CREATE INDEX IDX_CDE9C16438276D77 ON epreuve_echelle_epreuve_notation_directe (epreuve_echelle_id)');
        $this->addSql('CREATE INDEX IDX_CDE9C16491D59088 ON epreuve_echelle_epreuve_notation_directe (epreuve_notation_directe_id)');
        $this->addSql('ALTER TABLE epreuve_classe ADD CONSTRAINT FK_6AC91C21DD268C11 FOREIGN KEY (echelle_id) REFERENCES epreuve_echelle (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE epreuve_echelle ADD CONSTRAINT FK_8F8511C14BBC2705 FOREIGN KEY (version_id) REFERENCES epreuve_version (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE epreuve_echelle_epreuve_notation_directe ADD CONSTRAINT FK_CDE9C16438276D77 FOREIGN KEY (epreuve_echelle_id) REFERENCES epreuve_echelle (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE epreuve_echelle_epreuve_notation_directe ADD CONSTRAINT FK_CDE9C16491D59088 FOREIGN KEY (epreuve_notation_directe_id) REFERENCES epreuve_notation_directe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE epreuve_echelle_simple');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE epreuve_classe_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE epreuve_classe_critere_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE epreuve_echelle_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE epreuve_echelle_simple_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE epreuve_echelle_simple (id INT NOT NULL, code_epreuve SMALLINT NOT NULL, version SMALLINT NOT NULL, num_echelle SMALLINT NOT NULL, type_comptabilisation SMALLINT NOT NULL, question_associee SMALLINT NOT NULL, info_complementaire SMALLINT DEFAULT NULL, intitule VARCHAR(50) NOT NULL, abreviation VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE epreuve_classe DROP CONSTRAINT FK_6AC91C21DD268C11');
        $this->addSql('ALTER TABLE epreuve_echelle DROP CONSTRAINT FK_8F8511C14BBC2705');
        $this->addSql('ALTER TABLE epreuve_echelle_epreuve_notation_directe DROP CONSTRAINT FK_CDE9C16438276D77');
        $this->addSql('ALTER TABLE epreuve_echelle_epreuve_notation_directe DROP CONSTRAINT FK_CDE9C16491D59088');
        $this->addSql('DROP TABLE epreuve_classe');
        $this->addSql('DROP TABLE epreuve_classe_critere');
        $this->addSql('DROP TABLE epreuve_echelle');
        $this->addSql('DROP TABLE epreuve_echelle_epreuve_notation_directe');
    }
}
