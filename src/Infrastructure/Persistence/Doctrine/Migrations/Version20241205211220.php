<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241205211220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the points table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('points');
        $table->addColumn('id', 'string', ['length' => 255]);
        $table->addColumn('x', 'float');
        $table->addColumn('y', 'float');
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('points');
    }
}
