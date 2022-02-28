<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220228142529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pot ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE pot ADD CONSTRAINT FK_1EBD730FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1EBD730FA76ED395 ON pot (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pot DROP FOREIGN KEY FK_1EBD730FA76ED395');
        $this->addSql('DROP INDEX IDX_1EBD730FA76ED395 ON pot');
        $this->addSql('ALTER TABLE pot DROP user_id');
    }
}
