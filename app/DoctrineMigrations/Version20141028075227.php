<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141028075227 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("CREATE TABLE smq_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, last_login DATETIME DEFAULT NULL, salt LONGTEXT NOT NULL, password LONGTEXT NOT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', access_token VARCHAR(64) NOT NULL, password_token VARCHAR(64) DEFAULT NULL, password_request_at DATETIME DEFAULT NULL, locale VARCHAR(16) NOT NULL, timezone VARCHAR(32) NOT NULL, picture_url VARCHAR(200) DEFAULT NULL, UNIQUE INDEX UNIQ_7B76EB14E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE smq_project_role (user_id INT NOT NULL, project_id INT NOT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', access_token VARCHAR(64) NOT NULL, INDEX IDX_E8924F3BA76ED395 (user_id), INDEX IDX_E8924F3B166D1F9C (project_id), PRIMARY KEY(user_id, project_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE smq_message (id INT AUTO_INCREMENT NOT NULL, queue_id INT DEFAULT NULL, locked TINYINT(1) NOT NULL, retries_remaining INT NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, timeout_at DATETIME DEFAULT NULL, token VARCHAR(100) DEFAULT NULL, body LONGTEXT NOT NULL, INDEX IDX_6851D323477B5BAE (queue_id), INDEX idx_available_at (queue_id, available_at), INDEX idx_token (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE smq_queue (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, push_type VARCHAR(20) NOT NULL, retries INT NOT NULL, retries_delay INT NOT NULL, error_queue INT DEFAULT NULL, timeout INT NOT NULL, delay INT NOT NULL, expires_in INT NOT NULL, deleted_count INT DEFAULT 0 NOT NULL, INDEX IDX_6AD1B717166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE smq_project (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, title VARCHAR(60) NOT NULL, INDEX IDX_F15F33B27E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE smq_subscriber (id INT AUTO_INCREMENT NOT NULL, queue_id INT DEFAULT NULL, url VARCHAR(200) NOT NULL, headers LONGTEXT NOT NULL COMMENT '(DC2Type:array)', INDEX IDX_1449821F477B5BAE (queue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE smq_project_role ADD CONSTRAINT FK_E8924F3BA76ED395 FOREIGN KEY (user_id) REFERENCES smq_user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE smq_project_role ADD CONSTRAINT FK_E8924F3B166D1F9C FOREIGN KEY (project_id) REFERENCES smq_project (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE smq_message ADD CONSTRAINT FK_6851D323477B5BAE FOREIGN KEY (queue_id) REFERENCES smq_queue (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE smq_queue ADD CONSTRAINT FK_6AD1B717166D1F9C FOREIGN KEY (project_id) REFERENCES smq_project (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE smq_project ADD CONSTRAINT FK_F15F33B27E3C61F9 FOREIGN KEY (owner_id) REFERENCES smq_user (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE smq_subscriber ADD CONSTRAINT FK_1449821F477B5BAE FOREIGN KEY (queue_id) REFERENCES smq_queue (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE smq_project_role DROP FOREIGN KEY FK_E8924F3BA76ED395");
        $this->addSql("ALTER TABLE smq_project DROP FOREIGN KEY FK_F15F33B27E3C61F9");
        $this->addSql("ALTER TABLE smq_message DROP FOREIGN KEY FK_6851D323477B5BAE");
        $this->addSql("ALTER TABLE smq_subscriber DROP FOREIGN KEY FK_1449821F477B5BAE");
        $this->addSql("ALTER TABLE smq_project_role DROP FOREIGN KEY FK_E8924F3B166D1F9C");
        $this->addSql("ALTER TABLE smq_queue DROP FOREIGN KEY FK_6AD1B717166D1F9C");
        $this->addSql("DROP TABLE smq_user");
        $this->addSql("DROP TABLE smq_project_role");
        $this->addSql("DROP TABLE smq_message");
        $this->addSql("DROP TABLE smq_queue");
        $this->addSql("DROP TABLE smq_project");
        $this->addSql("DROP TABLE smq_subscriber");
    }
}
