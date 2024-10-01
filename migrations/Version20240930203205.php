<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240930203205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT NOT NULL, name VARCHAR(60) DEFAULT NULL, state VARCHAR(2) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, selected TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE configuration (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', param_name VARCHAR(255) NOT NULL, param_value VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, UNIQUE INDEX param_name_UNIQUE (param_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE temperature (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, date_time DATETIME DEFAULT CURRENT_TIMESTAMP, cpu NUMERIC(6, 3) DEFAULT NULL, gpu NUMERIC(6, 3) DEFAULT NULL, temperature NUMERIC(6, 3) DEFAULT NULL, sensation NUMERIC(6, 3) DEFAULT NULL, wind_direction VARCHAR(6) DEFAULT NULL, wind_velocity NUMERIC(6, 3) DEFAULT NULL, humidity NUMERIC(7, 3) DEFAULT NULL, weather_condition VARCHAR(60) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, pressure INT DEFAULT NULL, icon VARCHAR(2) DEFAULT NULL, INDEX IDX_BE4E2A6C8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE temperature ADD CONSTRAINT FK_BE4E2A6C8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE temperature DROP FOREIGN KEY FK_BE4E2A6C8BAC62AF');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE configuration');
        $this->addSql('DROP TABLE temperature');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
