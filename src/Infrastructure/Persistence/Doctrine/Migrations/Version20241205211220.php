<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205211220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the points table';
    }

    public function up(Schema $schema): void
    {
        // Create the "points" table
        $table = $schema->createTable('points');

        // Define the columns of the "points" table
        $table->addColumn('id', 'string', ['length' => 255]);
        $table->addColumn('x', 'float');
        $table->addColumn('y', 'float');

        // Define the primary key
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        // Drop the "points" table if rolling back
        $schema->dropTable('points');
    }
}
