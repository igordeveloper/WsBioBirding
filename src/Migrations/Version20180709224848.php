<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180709224848 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE popular_name (name VARCHAR(120) NOT NULL, scientific_name_id VARCHAR(120) NOT NULL, INDEX IDX_F9858108E9F9049C (scientific_name_id), PRIMARY KEY(name, scientific_name_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE species (scientific_name VARCHAR(120) NOT NULL, characteristics LONGTEXT NOT NULL, PRIMARY KEY(scientific_name)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE popular_name ADD CONSTRAINT FK_F9858108E9F9049C FOREIGN KEY (scientific_name_id) REFERENCES species (scientific_name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE popular_name DROP FOREIGN KEY FK_F9858108E9F9049C');
        $this->addSql('DROP TABLE popular_name');
        $this->addSql('DROP TABLE species');
    }
}
