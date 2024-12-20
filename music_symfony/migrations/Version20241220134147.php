<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241220134147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, commentor_id INT NOT NULL, song_id INT DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CFE65B143 (commentor_id), INDEX IDX_9474526CA0BDB2F3 (song_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist_song (playlist_id INT NOT NULL, song_id INT NOT NULL, INDEX IDX_93F4D9C36BBD148 (playlist_id), INDEX IDX_93F4D9C3A0BDB2F3 (song_id), PRIMARY KEY(playlist_id, song_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, artist VARCHAR(255) NOT NULL, album VARCHAR(255) DEFAULT NULL, release_date DATETIME NOT NULL, genre VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CFE65B143 FOREIGN KEY (commentor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
        $this->addSql('ALTER TABLE playlist_song ADD CONSTRAINT FK_93F4D9C36BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_song ADD CONSTRAINT FK_93F4D9C3A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CFE65B143');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA0BDB2F3');
        $this->addSql('ALTER TABLE playlist_song DROP FOREIGN KEY FK_93F4D9C36BBD148');
        $this->addSql('ALTER TABLE playlist_song DROP FOREIGN KEY FK_93F4D9C3A0BDB2F3');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE playlist');
        $this->addSql('DROP TABLE playlist_song');
        $this->addSql('DROP TABLE song');
        $this->addSql('DROP TABLE `user`');
    }
}
