<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240118151853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forma (id INT AUTO_INCREMENT NOT NULL, persona_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_E866ABFF5F88DB9 (persona_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formacion (id INT AUTO_INCREMENT NOT NULL, orden INT NOT NULL, description VARCHAR(510) NOT NULL, identifier VARCHAR(100) NOT NULL, parent VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formulario (id INT AUTO_INCREMENT NOT NULL, forma_id INT NOT NULL, pregunta VARCHAR(255) DEFAULT NULL, value VARCHAR(510) NOT NULL, INDEX IDX_24D6FBD338AD92B (forma_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personales (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, observaciones VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE forma ADD CONSTRAINT FK_E866ABFF5F88DB9 FOREIGN KEY (persona_id) REFERENCES personales (id)');
        $this->addSql('ALTER TABLE formulario ADD CONSTRAINT FK_24D6FBD338AD92B FOREIGN KEY (forma_id) REFERENCES forma (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forma DROP FOREIGN KEY FK_E866ABFF5F88DB9');
        $this->addSql('ALTER TABLE formulario DROP FOREIGN KEY FK_24D6FBD338AD92B');
        $this->addSql('DROP TABLE forma');
        $this->addSql('DROP TABLE formacion');
        $this->addSql('DROP TABLE formulario');
        $this->addSql('DROP TABLE personales');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
