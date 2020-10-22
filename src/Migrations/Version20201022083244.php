<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201022083244 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_category_id INT DEFAULT NULL, picture_id INT NOT NULL, title VARCHAR(70) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_64C19C1796A8F92 (parent_category_id), UNIQUE INDEX UNIQ_64C19C1EE45BDBF (picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, trick_id INT NOT NULL, message LONGTEXT NOT NULL, date_add DATETIME NOT NULL, date_update DATETIME DEFAULT NULL, INDEX IDX_FC16F1CCA76ED395 (user_id), INDEX IDX_FC16F1CCB281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE embed_video (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, url VARCHAR(255) NOT NULL, youtube_id VARCHAR(255) NOT NULL, INDEX IDX_BC47FC4EB281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_16DB4F89B281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, updated_by_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, date_add DATETIME NOT NULL, date_update DATETIME DEFAULT NULL, visible TINYINT(1) NOT NULL, difficulty SMALLINT NOT NULL, INDEX IDX_D8F0A91EF675F31B (author_id), INDEX IDX_D8F0A91E896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick_category (trick_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_639F9D7EB281BE2E (trick_id), INDEX IDX_639F9D7E12469DE2 (category_id), PRIMARY KEY(trick_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1796A8F92 FOREIGN KEY (parent_category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id)');
        $this->addSql('ALTER TABLE chat_post ADD CONSTRAINT FK_FC16F1CCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE chat_post ADD CONSTRAINT FK_FC16F1CCB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE embed_video ADD CONSTRAINT FK_BC47FC4EB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trick_category ADD CONSTRAINT FK_639F9D7EB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_category ADD CONSTRAINT FK_639F9D7E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1796A8F92');
        $this->addSql('ALTER TABLE trick_category DROP FOREIGN KEY FK_639F9D7E12469DE2');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1EE45BDBF');
        $this->addSql('ALTER TABLE chat_post DROP FOREIGN KEY FK_FC16F1CCB281BE2E');
        $this->addSql('ALTER TABLE embed_video DROP FOREIGN KEY FK_BC47FC4EB281BE2E');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B281BE2E');
        $this->addSql('ALTER TABLE trick_category DROP FOREIGN KEY FK_639F9D7EB281BE2E');
        $this->addSql('ALTER TABLE chat_post DROP FOREIGN KEY FK_FC16F1CCA76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EF675F31B');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E896DBBDE');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE chat_post');
        $this->addSql('DROP TABLE embed_video');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE trick');
        $this->addSql('DROP TABLE trick_category');
        $this->addSql('DROP TABLE user');
    }
}
