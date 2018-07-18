<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180716151908 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE popular_name DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE popular_name ADD PRIMARY KEY (scientific_name, name)');
        $this->addSql('ALTER TABLE species CHANGE characteristics characteristics LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE popular_name DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE popular_name ADD PRIMARY KEY (name, scientific_name)');
        $this->addSql('ALTER TABLE species CHANGE characteristics characteristics LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
