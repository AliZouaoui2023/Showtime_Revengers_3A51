<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217092538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, admin_id INT DEFAULT NULL, nbr_jours INT NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, lien_supp VARCHAR(255) DEFAULT NULL, statut VARCHAR(255) DEFAULT \'en_attente\' NOT NULL, date_soumission DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_2694D7A5A76ED395 (user_id), INDEX IDX_2694D7A5642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publicite (id INT AUTO_INCREMENT NOT NULL, demande_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, support VARCHAR(255) NOT NULL, montant DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_1D394E3980E95E18 (demande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date_de_naissance DATE NOT NULL, email VARCHAR(255) DEFAULT NULL, role VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A5642B8210 FOREIGN KEY (admin_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE publicite ADD CONSTRAINT FK_1D394E3980E95E18 FOREIGN KEY (demande_id) REFERENCES demande (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A5A76ED395');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A5642B8210');
        $this->addSql('ALTER TABLE publicite DROP FOREIGN KEY FK_1D394E3980E95E18');
        $this->addSql('DROP TABLE demande');
        $this->addSql('DROP TABLE publicite');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
