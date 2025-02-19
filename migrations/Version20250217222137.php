<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217222137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipement ADD CONSTRAINT FK_B8B4C6F3DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id_salle)');
        $this->addSql('CREATE INDEX IDX_B8B4C6F3DC304035 ON equipement (salle_id)');
        $this->addSql('ALTER TABLE salle ADD nombre_de_place INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipement DROP FOREIGN KEY FK_B8B4C6F3DC304035');
        $this->addSql('DROP INDEX IDX_B8B4C6F3DC304035 ON equipement');
        $this->addSql('ALTER TABLE salle DROP nombre_de_place');
    }
}
