<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220807173224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_scan (id INT AUTO_INCREMENT NOT NULL, contest_id INT NOT NULL, dancer_id INT NOT NULL, scanned_by_id INT DEFAULT NULL, INDEX IDX_DB78FF051CD0F0DE (contest_id), INDEX IDX_DB78FF05A7CAA267 (dancer_id), INDEX IDX_DB78FF05EBBC642F (scanned_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_scan ADD CONSTRAINT FK_DB78FF051CD0F0DE FOREIGN KEY (contest_id) REFERENCES contest (id)');
        $this->addSql('ALTER TABLE event_scan ADD CONSTRAINT FK_DB78FF05A7CAA267 FOREIGN KEY (dancer_id) REFERENCES dancers (id)');
        $this->addSql('ALTER TABLE event_scan ADD CONSTRAINT FK_DB78FF05EBBC642F FOREIGN KEY (scanned_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE event_scan');
    }
}
