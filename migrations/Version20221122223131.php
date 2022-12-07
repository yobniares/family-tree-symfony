<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221122223131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE human ADD mother_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE human ADD father_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE human ADD CONSTRAINT FK_A562D5F5B78A354D FOREIGN KEY (mother_id) REFERENCES human (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE human ADD CONSTRAINT FK_A562D5F52055B9A2 FOREIGN KEY (father_id) REFERENCES human (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A562D5F5B78A354D ON human (mother_id)');
        $this->addSql('CREATE INDEX IDX_A562D5F52055B9A2 ON human (father_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE human DROP CONSTRAINT FK_A562D5F5B78A354D');
        $this->addSql('ALTER TABLE human DROP CONSTRAINT FK_A562D5F52055B9A2');
        $this->addSql('DROP INDEX IDX_A562D5F5B78A354D');
        $this->addSql('DROP INDEX IDX_A562D5F52055B9A2');
        $this->addSql('ALTER TABLE human DROP mother_id');
        $this->addSql('ALTER TABLE human DROP father_id');
    }
}
