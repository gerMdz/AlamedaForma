<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240523223951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forma (id INT AUTO_INCREMENT NOT NULL, persona_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_E866ABFF5F88DB9 (persona_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formacion (id INT AUTO_INCREMENT NOT NULL, orden INT NOT NULL, description VARCHAR(510) NOT NULL, identifier VARCHAR(100) NOT NULL, parent VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formulario (id INT AUTO_INCREMENT NOT NULL, forma_id INT NOT NULL, pregunta VARCHAR(255) DEFAULT NULL, value VARCHAR(510) NOT NULL, INDEX IDX_24D6FBD338AD92B (forma_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inicio (id VARCHAR(255) NOT NULL, organization_id VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, terms LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_DDB657CA32C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_disabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', identifier VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personales (id VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, observaciones VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE forma ADD CONSTRAINT FK_E866ABFF5F88DB9 FOREIGN KEY (persona_id) REFERENCES personales (id)');
        $this->addSql('ALTER TABLE formulario ADD CONSTRAINT FK_24D6FBD338AD92B FOREIGN KEY (forma_id) REFERENCES forma (id)');
        $this->addSql('ALTER TABLE inicio ADD CONSTRAINT FK_DDB657CA32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forma DROP FOREIGN KEY FK_E866ABFF5F88DB9');
        $this->addSql('ALTER TABLE formulario DROP FOREIGN KEY FK_24D6FBD338AD92B');
        $this->addSql('ALTER TABLE inicio DROP FOREIGN KEY FK_DDB657CA32C8A3DE');
        $this->addSql('DROP TABLE forma');
        $this->addSql('DROP TABLE formacion');
        $this->addSql('DROP TABLE formulario');
        $this->addSql('DROP TABLE inicio');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE personales');
    }
}
