<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240628125711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE next_of_kin DROP FOREIGN KEY FK_CE1E3CF9A76ED395');
        $this->addSql('DROP INDEX IDX_CE1E3CF9A76ED395 ON next_of_kin');
        $this->addSql('ALTER TABLE next_of_kin DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE next_of_kin ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE next_of_kin ADD CONSTRAINT FK_CE1E3CF9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CE1E3CF9A76ED395 ON next_of_kin (user_id)');
    }
}
