<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221122214651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE human_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE human (id INT NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, middlename VARCHAR(255) DEFAULT NULL, maidenname VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) NOT NULL, year_birth INT DEFAULT NULL, month_birth INT DEFAULT NULL, day_birth INT DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, description VARCHAR(500) DEFAULT NULL, datetime_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE human_id_seq CASCADE');
        $this->addSql('DROP TABLE human');
    }
}
