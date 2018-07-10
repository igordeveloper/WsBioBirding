<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180710012208 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE popular_name DROP FOREIGN KEY FK_F9858108E9F9049C');
        $this->addSql('DROP INDEX IDX_F9858108E9F9049C ON popular_name');
        $this->addSql('ALTER TABLE popular_name DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE popular_name CHANGE scientific_name_id scientific_name VARCHAR(120) NOT NULL');
        $this->addSql('ALTER TABLE popular_name ADD CONSTRAINT FK_F985810847F759B3 FOREIGN KEY (scientific_name) REFERENCES species (scientific_name)');
        $this->addSql('CREATE INDEX IDX_F985810847F759B3 ON popular_name (scientific_name)');
        $this->addSql('ALTER TABLE popular_name ADD PRIMARY KEY (name, scientific_name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE popular_name DROP FOREIGN KEY FK_F985810847F759B3');
        $this->addSql('DROP INDEX IDX_F985810847F759B3 ON popular_name');
        $this->addSql('ALTER TABLE popular_name DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE popular_name CHANGE scientific_name scientific_name_id VARCHAR(120) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE popular_name ADD CONSTRAINT FK_F9858108E9F9049C FOREIGN KEY (scientific_name_id) REFERENCES species (scientific_name)');
        $this->addSql('CREATE INDEX IDX_F9858108E9F9049C ON popular_name (scientific_name_id)');
        $this->addSql('ALTER TABLE popular_name ADD PRIMARY KEY (name, scientific_name_id)');
    }
}
