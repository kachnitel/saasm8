<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241115034850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change billing_category color to VARCHAR(32)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__billing_category AS SELECT id, name, rate, color FROM billing_category');
        $this->addSql('DROP TABLE billing_category');
        $this->addSql('CREATE TABLE billing_category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(32) NOT NULL, rate DOUBLE PRECISION DEFAULT NULL, color VARCHAR(32) NOT NULL)');
        $this->addSql('INSERT INTO billing_category (id, name, rate, color) SELECT id, name, rate, color FROM __temp__billing_category');
        $this->addSql('DROP TABLE __temp__billing_category');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__billing_category AS SELECT id, name, rate, color FROM billing_category');
        $this->addSql('DROP TABLE billing_category');
        $this->addSql('CREATE TABLE billing_category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(32) NOT NULL, rate DOUBLE PRECISION DEFAULT NULL, color VARCHAR(6) NOT NULL)');
        $this->addSql('INSERT INTO billing_category (id, name, rate, color) SELECT id, name, rate, color FROM __temp__billing_category');
        $this->addSql('DROP TABLE __temp__billing_category');
    }
}
