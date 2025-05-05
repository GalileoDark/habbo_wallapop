<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502165536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE inventario (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, objeto_id INT NOT NULL, cantidad INT NOT NULL, INDEX IDX_6A194EF5DB38439E (usuario_id), INDEX IDX_6A194EF576F5CD27 (objeto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE mensaje (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, publicacion_id INT NOT NULL, contenido LONGTEXT NOT NULL, fecha_envio DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_9B631D01DB38439E (usuario_id), INDEX IDX_9B631D019ACBB5E7 (publicacion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE objeto (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, precio_catalogo DOUBLE PRECISION NOT NULL, precio_medio DOUBLE PRECISION NOT NULL, fecha_salida DATE DEFAULT NULL, INDEX IDX_274BE6963397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE publicacion (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, objeto_id INT NOT NULL, descripcion LONGTEXT DEFAULT NULL, busca_objeto VARCHAR(255) DEFAULT NULL, INDEX IDX_62F2085FDB38439E (usuario_id), INDEX IDX_62F2085F76F5CD27 (objeto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, emial VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, nombre_usuario VARCHAR(255) NOT NULL, pais VARCHAR(255) DEFAULT NULL, foto_perfil VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inventario ADD CONSTRAINT FK_6A194EF5DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inventario ADD CONSTRAINT FK_6A194EF576F5CD27 FOREIGN KEY (objeto_id) REFERENCES objeto (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D01DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D019ACBB5E7 FOREIGN KEY (publicacion_id) REFERENCES publicacion (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objeto ADD CONSTRAINT FK_274BE6963397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE publicacion ADD CONSTRAINT FK_62F2085FDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE publicacion ADD CONSTRAINT FK_62F2085F76F5CD27 FOREIGN KEY (objeto_id) REFERENCES objeto (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE inventario DROP FOREIGN KEY FK_6A194EF5DB38439E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inventario DROP FOREIGN KEY FK_6A194EF576F5CD27
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D01DB38439E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D019ACBB5E7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE objeto DROP FOREIGN KEY FK_274BE6963397707A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE publicacion DROP FOREIGN KEY FK_62F2085FDB38439E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE publicacion DROP FOREIGN KEY FK_62F2085F76F5CD27
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categoria
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE inventario
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE mensaje
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE objeto
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE publicacion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE usuario
        SQL);
    }
}
