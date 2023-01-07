<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230106233526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dancers_team DROP FOREIGN KEY FK_5AB660E9296CD8AE');
        $this->addSql('ALTER TABLE dancers_team DROP FOREIGN KEY FK_5AB660E93400837C');
        $this->addSql('DROP TABLE dancers_team');
        $this->addSql('ALTER TABLE dancers ADD team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dancers ADD CONSTRAINT FK_B4B5C88F296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_B4B5C88F296CD8AE ON dancers (team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dancers_team (dancers_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_5AB660E93400837C (dancers_id), INDEX IDX_5AB660E9296CD8AE (team_id), PRIMARY KEY(dancers_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE dancers_team ADD CONSTRAINT FK_5AB660E9296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dancers_team ADD CONSTRAINT FK_5AB660E93400837C FOREIGN KEY (dancers_id) REFERENCES dancers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dancers DROP FOREIGN KEY FK_B4B5C88F296CD8AE');
        $this->addSql('DROP INDEX IDX_B4B5C88F296CD8AE ON dancers');
        $this->addSql('ALTER TABLE dancers DROP team_id');
    }
}
