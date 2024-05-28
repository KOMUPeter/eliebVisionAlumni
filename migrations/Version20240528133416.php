<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240528133416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, event_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_message (id INT AUTO_INCREMENT NOT NULL, group_sender_id INT DEFAULT NULL, group_content VARCHAR(255) NOT NULL, INDEX IDX_30BD64733697EF7C (group_sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_message_users (group_message_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_B4CC944084B7729B (group_message_id), INDEX IDX_B4CC944067B3B43D (users_id), PRIMARY KEY(group_message_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, size INT NOT NULL, uploaded_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images_group_message (images_id INT NOT NULL, group_message_id INT NOT NULL, INDEX IDX_C54B644DD44F05E5 (images_id), INDEX IDX_C54B644D84B7729B (group_message_id), PRIMARY KEY(images_id, group_message_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images_private_message (images_id INT NOT NULL, private_message_id INT NOT NULL, INDEX IDX_6811210D44F05E5 (images_id), INDEX IDX_68112105EBFB95E (private_message_id), PRIMARY KEY(images_id, private_message_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE next_of_kin (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, next_of_kin_first_name VARCHAR(255) NOT NULL, next_of_kin_last_name VARCHAR(255) NOT NULL, next_of_kin_email VARCHAR(255) DEFAULT NULL, next_of_kin_phone INT DEFAULT NULL, INDEX IDX_CE1E3CF9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payout (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, paydate DATETIME NOT NULL, amount INT NOT NULL, INDEX IDX_4E2EA902A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE private_message (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, recipient_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, sent_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4744FC9BF624B39D (sender_id), INDEX IDX_4744FC9BE92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, profile_image_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_names VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone_number INT NOT NULL, registration_date DATETIME NOT NULL, is_subscribed TINYINT(1) NOT NULL, INDEX IDX_1483A5E9C4CF44DC (profile_image_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_message ADD CONSTRAINT FK_30BD64733697EF7C FOREIGN KEY (group_sender_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE group_message_users ADD CONSTRAINT FK_B4CC944084B7729B FOREIGN KEY (group_message_id) REFERENCES group_message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_message_users ADD CONSTRAINT FK_B4CC944067B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE images_group_message ADD CONSTRAINT FK_C54B644DD44F05E5 FOREIGN KEY (images_id) REFERENCES images (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE images_group_message ADD CONSTRAINT FK_C54B644D84B7729B FOREIGN KEY (group_message_id) REFERENCES group_message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE images_private_message ADD CONSTRAINT FK_6811210D44F05E5 FOREIGN KEY (images_id) REFERENCES images (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE images_private_message ADD CONSTRAINT FK_68112105EBFB95E FOREIGN KEY (private_message_id) REFERENCES private_message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE next_of_kin ADD CONSTRAINT FK_CE1E3CF9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE payout ADD CONSTRAINT FK_4E2EA902A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9BF624B39D FOREIGN KEY (sender_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9BE92F8F78 FOREIGN KEY (recipient_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9C4CF44DC FOREIGN KEY (profile_image_id) REFERENCES images (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_message DROP FOREIGN KEY FK_30BD64733697EF7C');
        $this->addSql('ALTER TABLE group_message_users DROP FOREIGN KEY FK_B4CC944084B7729B');
        $this->addSql('ALTER TABLE group_message_users DROP FOREIGN KEY FK_B4CC944067B3B43D');
        $this->addSql('ALTER TABLE images_group_message DROP FOREIGN KEY FK_C54B644DD44F05E5');
        $this->addSql('ALTER TABLE images_group_message DROP FOREIGN KEY FK_C54B644D84B7729B');
        $this->addSql('ALTER TABLE images_private_message DROP FOREIGN KEY FK_6811210D44F05E5');
        $this->addSql('ALTER TABLE images_private_message DROP FOREIGN KEY FK_68112105EBFB95E');
        $this->addSql('ALTER TABLE next_of_kin DROP FOREIGN KEY FK_CE1E3CF9A76ED395');
        $this->addSql('ALTER TABLE payout DROP FOREIGN KEY FK_4E2EA902A76ED395');
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9BF624B39D');
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9BE92F8F78');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9C4CF44DC');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE group_message');
        $this->addSql('DROP TABLE group_message_users');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE images_group_message');
        $this->addSql('DROP TABLE images_private_message');
        $this->addSql('DROP TABLE next_of_kin');
        $this->addSql('DROP TABLE payout');
        $this->addSql('DROP TABLE private_message');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
