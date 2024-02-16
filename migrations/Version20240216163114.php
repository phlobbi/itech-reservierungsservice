<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216163114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE restaurant_available_time (id INT AUTO_INCREMENT NOT NULL, restaurant_table_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_43681DC5CC5AE6B3 (restaurant_table_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_reserveration (id INT AUTO_INCREMENT NOT NULL, restaurant_available_time_id INT NOT NULL, guests INT NOT NULL, special_wishes VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_276A03E34D2014AE (restaurant_available_time_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_table (id INT AUTO_INCREMENT NOT NULL, size INT NOT NULL, is_outside TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE restaurant_available_time ADD CONSTRAINT FK_43681DC5CC5AE6B3 FOREIGN KEY (restaurant_table_id) REFERENCES restaurant_table (id)');
        $this->addSql('ALTER TABLE restaurant_reserveration ADD CONSTRAINT FK_276A03E34D2014AE FOREIGN KEY (restaurant_available_time_id) REFERENCES restaurant_available_time (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant_available_time DROP FOREIGN KEY FK_43681DC5CC5AE6B3');
        $this->addSql('ALTER TABLE restaurant_reserveration DROP FOREIGN KEY FK_276A03E34D2014AE');
        $this->addSql('DROP TABLE restaurant_available_time');
        $this->addSql('DROP TABLE restaurant_reserveration');
        $this->addSql('DROP TABLE restaurant_table');
    }
}
