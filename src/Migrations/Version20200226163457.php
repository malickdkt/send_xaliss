<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200226163457 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tarif (id INT AUTO_INCREMENT NOT NULL, borne_inf DOUBLE PRECISION NOT NULL, borne_sup DOUBLE PRECISION NOT NULL, frais DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, envoi_id INT DEFAULT NULL, retrait_id INT DEFAULT NULL, user_envoi_id INT NOT NULL, user_retrait_id INT DEFAULT NULL, prenom_e VARCHAR(255) NOT NULL, nom_e VARCHAR(255) NOT NULL, telephone_e INT NOT NULL, npiece_e INT NOT NULL, prenom_b VARCHAR(255) NOT NULL, nom_b VARCHAR(255) NOT NULL, telephone_b INT NOT NULL, npiece_b INT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, frais DOUBLE PRECISION NOT NULL, etat TINYINT(1) NOT NULL, date_envoi DATETIME NOT NULL, date_retrait DATETIME DEFAULT NULL, com_etat DOUBLE PRECISION NOT NULL, com_systeme DOUBLE PRECISION NOT NULL, com_envoi DOUBLE PRECISION NOT NULL, com_retrait DOUBLE PRECISION DEFAULT NULL, code INT NOT NULL, INDEX IDX_723705D13F97ECE5 (envoi_id), INDEX IDX_723705D17EF8457A (retrait_id), INDEX IDX_723705D1DF1A08E5 (user_envoi_id), INDEX IDX_723705D1D99F8396 (user_retrait_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE affectation (id INT AUTO_INCREMENT NOT NULL, compte_id INT DEFAULT NULL, user_id INT DEFAULT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, INDEX IDX_F4DD61D3F2C56620 (compte_id), INDEX IDX_F4DD61D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D13F97ECE5 FOREIGN KEY (envoi_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D17EF8457A FOREIGN KEY (retrait_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1DF1A08E5 FOREIGN KEY (user_envoi_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1D99F8396 FOREIGN KEY (user_retrait_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tarif');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE affectation');
    }
}
