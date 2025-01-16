<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116140704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE channel_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE video_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE channel (id INT NOT NULL, name VARCHAR(255) NOT NULL, channel_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE video (id INT NOT NULL, channel_id_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, yt_id VARCHAR(255) NOT NULL, thumbnail_url VARCHAR(255) NOT NULL, publish_time VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CC7DA2CC86596CF ON video (channel_id_id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CC86596CF FOREIGN KEY (channel_id_id) REFERENCES channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE channel_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE video_id_seq CASCADE');
        $this->addSql('ALTER TABLE video DROP CONSTRAINT FK_7CC7DA2CC86596CF');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE video');
    }
}
