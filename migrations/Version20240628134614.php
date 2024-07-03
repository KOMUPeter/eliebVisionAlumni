<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240628134614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users_next_of_kin DROP FOREIGN KEY FK_39AB000B55A8C93D');
        $this->addSql('ALTER TABLE users_next_of_kin DROP FOREIGN KEY FK_39AB000B67B3B43D');
        $this->addSql('DROP TABLE users_next_of_kin');
        $this->addSql('DROP TABLE next_of_kin');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users_next_of_kin (users_id INT NOT NULL, next_of_kin_id INT NOT NULL, INDEX IDX_39AB000B67B3B43D (users_id), INDEX IDX_39AB000B55A8C93D (next_of_kin_id), PRIMARY KEY(users_id, next_of_kin_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE next_of_kin (id INT AUTO_INCREMENT NOT NULL, next_of_kin_first_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, next_of_kin_last_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, next_of_kin_email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, next_of_kin_phone INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE users_next_of_kin ADD CONSTRAINT FK_39AB000B55A8C93D FOREIGN KEY (next_of_kin_id) REFERENCES next_of_kin (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_next_of_kin ADD CONSTRAINT FK_39AB000B67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
