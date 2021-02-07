<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210118191548 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cyclic (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, periodicity INT NOT NULL, INDEX IDX_C3BAD44CC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cyclic_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reminder (id INT AUTO_INCREMENT NOT NULL, cyclic_id INT DEFAULT NULL, user_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, remind_at DATETIME NOT NULL, pre_remind_at DATETIME DEFAULT NULL, pre_reminded TINYINT(1) NOT NULL, channels LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_40374F40D121EBDE (cyclic_id), INDEX IDX_40374F40A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO cyclic_type (id, name) VALUES (null, "Day"), (null, "Week"), (null, "Month"), (null, "Year")');
        $this->addSql('ALTER TABLE cyclic ADD CONSTRAINT FK_C3BAD44CC54C8C93 FOREIGN KEY (type_id) REFERENCES cyclic_type (id)');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F40D121EBDE FOREIGN KEY (cyclic_id) REFERENCES cyclic (id)');
        $this->addSql('ALTER TABLE reminder ADD CONSTRAINT FK_40374F40A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reminder DROP FOREIGN KEY FK_40374F40D121EBDE');
        $this->addSql('ALTER TABLE cyclic DROP FOREIGN KEY FK_C3BAD44CC54C8C93');
        $this->addSql('ALTER TABLE reminder DROP FOREIGN KEY FK_40374F40A76ED395');
        $this->addSql('DROP TABLE cyclic');
        $this->addSql('DROP TABLE cyclic_type');
        $this->addSql('DROP TABLE reminder');
        $this->addSql('DROP TABLE user');
    }
}
