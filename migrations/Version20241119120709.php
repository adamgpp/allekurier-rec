<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241119120709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add `status` field to `users` table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD status VARCHAR(255) NOT NULL');
    }
}
