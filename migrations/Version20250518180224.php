<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518180224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D01DB38439E
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9B631D01DB38439E ON mensaje
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje ADD receptor_id INT NOT NULL, CHANGE usuario_id emisor_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D016BDF87DF FOREIGN KEY (emisor_id) REFERENCES usuario (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D01386D8D01 FOREIGN KEY (receptor_id) REFERENCES usuario (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9B631D016BDF87DF ON mensaje (emisor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9B631D01386D8D01 ON mensaje (receptor_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D016BDF87DF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D01386D8D01
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9B631D016BDF87DF ON mensaje
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9B631D01386D8D01 ON mensaje
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje ADD usuario_id INT NOT NULL, DROP emisor_id, DROP receptor_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D01DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9B631D01DB38439E ON mensaje (usuario_id)
        SQL);
    }
}
