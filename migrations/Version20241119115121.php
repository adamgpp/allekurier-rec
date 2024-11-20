<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241119115121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create database schema with ULID type `id` fields.';
    }

    public function up(Schema $schema): void
    {
        $invoicesSql = <<<SQL
            CREATE TABLE invoices (
                id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', 
                user_id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', 
                amount INT UNSIGNED NOT NULL, 
                status VARCHAR(16) NOT NULL, 
                INDEX IDX_6A2F2F95A76ED395 (user_id), 
                PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL;

        $this->addSql($invoicesSql);

        $usersSql = <<<SQL
            CREATE TABLE users (
                id BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)', 
                email VARCHAR(300) NOT NULL, 
                PRIMARY KEY(id)) 
                DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL;

        $this->addSql($usersSql);

        $messengerMessegesSql = <<<SQL
            CREATE TABLE messenger_messages (
                id BIGINT AUTO_INCREMENT NOT NULL, 
                body LONGTEXT NOT NULL, 
                headers LONGTEXT NOT NULL, 
                queue_name VARCHAR(190) NOT NULL, 
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
                available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
                delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', 
                INDEX IDX_75EA56E0FB7336F0 (queue_name), 
                INDEX IDX_75EA56E0E3BD61CE (available_at), 
                INDEX IDX_75EA56E016BA31DB (delivered_at), 
                PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL;

        $this->addSql($messengerMessegesSql);

        $this->addSql('ALTER TABLE invoices ADD CONSTRAINT FK_6A2F2F95A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }
}
