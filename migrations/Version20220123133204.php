<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220123133204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE buyer (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, fullname VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(45) DEFAULT NULL, age VARCHAR(45) DEFAULT NULL, INDEX IDX_84905FB319EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, fullname VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, email VARCHAR(255) NOT NULL, api_key VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operating_system (id INT AUTO_INCREMENT NOT NULL, operating_system VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, resolution_id_id INT NOT NULL, operating_system_id_id INT NOT NULL, fullname VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, weight DOUBLE PRECISION DEFAULT NULL, screen_size DOUBLE PRECISION DEFAULT NULL, storage INT NOT NULL, battery INT DEFAULT NULL, ram VARCHAR(45) NOT NULL, image JSON DEFAULT NULL, INDEX IDX_D34A04AD44F5D008 (brand_id), INDEX IDX_D34A04AD41FA2450 (resolution_id_id), INDEX IDX_D34A04ADD5BDA869 (operating_system_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resolution (id INT AUTO_INCREMENT NOT NULL, height VARCHAR(45) NOT NULL, width VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE buyer ADD CONSTRAINT FK_84905FB319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD41FA2450 FOREIGN KEY (resolution_id_id) REFERENCES resolution (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADD5BDA869 FOREIGN KEY (operating_system_id_id) REFERENCES operating_system (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('ALTER TABLE buyer DROP FOREIGN KEY FK_84905FB319EB6921');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADD5BDA869');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD41FA2450');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE buyer');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE operating_system');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE resolution');
    }
}
