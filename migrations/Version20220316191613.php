<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220316191613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE archivo (id INT AUTO_INCREMENT NOT NULL, publicacion_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, url LONGTEXT NOT NULL, INDEX IDX_3529B4829ACBB5E7 (publicacion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE banner (id INT AUTO_INCREMENT NOT NULL, imagen_id INT DEFAULT NULL, publicacion_id INT DEFAULT NULL, nombre LONGTEXT NOT NULL, activo TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_6F9DB8E7763C8AA7 (imagen_id), INDEX IDX_6F9DB8E79ACBB5E7 (publicacion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE imagen (id INT AUTO_INCREMENT NOT NULL, publicacion_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, url LONGTEXT NOT NULL, INDEX IDX_8319D2B39ACBB5E7 (publicacion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, orden INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_publicacion (menu_id INT NOT NULL, publicacion_id INT NOT NULL, INDEX IDX_A0E1613CCD7E912 (menu_id), INDEX IDX_A0E16139ACBB5E7 (publicacion_id), PRIMARY KEY(menu_id, publicacion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publicacion (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, cuerpo LONGTEXT NOT NULL, titulo LONGTEXT NOT NULL, flag_noticia TINYINT(1) NOT NULL, descripcion LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2265B05DF85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE archivo ADD CONSTRAINT FK_3529B4829ACBB5E7 FOREIGN KEY (publicacion_id) REFERENCES publicacion (id)');
        $this->addSql('ALTER TABLE banner ADD CONSTRAINT FK_6F9DB8E7763C8AA7 FOREIGN KEY (imagen_id) REFERENCES imagen (id)');
        $this->addSql('ALTER TABLE banner ADD CONSTRAINT FK_6F9DB8E79ACBB5E7 FOREIGN KEY (publicacion_id) REFERENCES publicacion (id)');
        $this->addSql('ALTER TABLE imagen ADD CONSTRAINT FK_8319D2B39ACBB5E7 FOREIGN KEY (publicacion_id) REFERENCES publicacion (id)');
        $this->addSql('ALTER TABLE menu_publicacion ADD CONSTRAINT FK_A0E1613CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_publicacion ADD CONSTRAINT FK_A0E16139ACBB5E7 FOREIGN KEY (publicacion_id) REFERENCES publicacion (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banner DROP FOREIGN KEY FK_6F9DB8E7763C8AA7');
        $this->addSql('ALTER TABLE menu_publicacion DROP FOREIGN KEY FK_A0E1613CCD7E912');
        $this->addSql('ALTER TABLE archivo DROP FOREIGN KEY FK_3529B4829ACBB5E7');
        $this->addSql('ALTER TABLE banner DROP FOREIGN KEY FK_6F9DB8E79ACBB5E7');
        $this->addSql('ALTER TABLE imagen DROP FOREIGN KEY FK_8319D2B39ACBB5E7');
        $this->addSql('ALTER TABLE menu_publicacion DROP FOREIGN KEY FK_A0E16139ACBB5E7');
        $this->addSql('DROP TABLE archivo');
        $this->addSql('DROP TABLE banner');
        $this->addSql('DROP TABLE imagen');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_publicacion');
        $this->addSql('DROP TABLE publicacion');
        $this->addSql('DROP TABLE usuario');
    }
}
