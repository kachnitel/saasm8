<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241114041401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initialize billing_category, time_entry, and messenger_messages tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE billing_category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(32) NOT NULL, rate DOUBLE PRECISION DEFAULT NULL, color VARCHAR(6) NOT NULL)');
        $this->addSql('CREATE TABLE time_entry (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, billing_category_id INTEGER NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_6E537C0C95C2595F FOREIGN KEY (billing_category_id) REFERENCES billing_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6E537C0C95C2595F ON time_entry (billing_category_id)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE billing_category');
        $this->addSql('DROP TABLE time_entry');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
