<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180811163749 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE popular_name (name VARCHAR(120) NOT NULL, scientific_name VARCHAR(120) NOT NULL, INDEX IDX_F985810847F759B3 (scientific_name), PRIMARY KEY(name, scientific_name)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (rg VARCHAR(13) NOT NULL, access_level INT NOT NULL, full_name VARCHAR(60) NOT NULL, email VARCHAR(150) NOT NULL, nickname VARCHAR(12) NOT NULL, cr_bio VARCHAR(15) DEFAULT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649A188FE64 (nickname), UNIQUE INDEX UNIQ_8D93D649DBFC365 (cr_bio), INDEX IDX_8D93D649737FB040 (access_level), PRIMARY KEY(rg, access_level)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE access_level (access_level INT AUTO_INCREMENT NOT NULL, description VARCHAR(25) NOT NULL, PRIMARY KEY(access_level)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE species (scientific_name VARCHAR(120) NOT NULL, characteristics LONGTEXT NOT NULL, PRIMARY KEY(scientific_name)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE popular_name ADD CONSTRAINT FK_F985810847F759B3 FOREIGN KEY (scientific_name) REFERENCES species (scientific_name)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649737FB040 FOREIGN KEY (access_level) REFERENCES access_level (access_level)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649737FB040');
        $this->addSql('ALTER TABLE popular_name DROP FOREIGN KEY FK_F985810847F759B3');
        $this->addSql('DROP TABLE popular_name');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE access_level');
        $this->addSql('DROP TABLE species');
    }
}
