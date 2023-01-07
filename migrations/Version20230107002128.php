<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230107002128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dancers_team (dancers_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_5AB660E93400837C (dancers_id), INDEX IDX_5AB660E9296CD8AE (team_id), PRIMARY KEY(dancers_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dancers_team ADD CONSTRAINT FK_5AB660E93400837C FOREIGN KEY (dancers_id) REFERENCES dancers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dancers_team ADD CONSTRAINT FK_5AB660E9296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dancers_team DROP FOREIGN KEY FK_5AB660E93400837C');
        $this->addSql('ALTER TABLE dancers_team DROP FOREIGN KEY FK_5AB660E9296CD8AE');
        $this->addSql('DROP TABLE dancers_team');
    }
}
