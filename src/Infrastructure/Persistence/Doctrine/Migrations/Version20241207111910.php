<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241207111910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the point_unions table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE point_unions (id VARCHAR(255) NOT NULL, point1_id VARCHAR(255) DEFAULT NULL, point2_id VARCHAR(255) DEFAULT NULL, distance DOUBLE PRECISION NOT NULL, INDEX IDX_925D51B8EE74C799 (point1_id), INDEX IDX_925D51B8FCC16877 (point2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE point_unions ADD CONSTRAINT FK_925D51B8EE74C799 FOREIGN KEY (point1_id) REFERENCES points (id)');
        $this->addSql('ALTER TABLE point_unions ADD CONSTRAINT FK_925D51B8FCC16877 FOREIGN KEY (point2_id) REFERENCES points (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE point_unions DROP FOREIGN KEY FK_925D51B8EE74C799');
        $this->addSql('ALTER TABLE point_unions DROP FOREIGN KEY FK_925D51B8FCC16877');
        $this->addSql('DROP TABLE point_unions');
    }
}
