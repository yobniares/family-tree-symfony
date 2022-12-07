<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221123110149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE human ADD year_death INT DEFAULT NULL');
        $this->addSql('ALTER TABLE human ADD month_death INT DEFAULT NULL');
        $this->addSql('ALTER TABLE human ADD day_death INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE human DROP year_death');
        $this->addSql('ALTER TABLE human DROP month_death');
        $this->addSql('ALTER TABLE human DROP day_death');
    }
}
