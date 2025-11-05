<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250517152224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD profile_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment ADD CONSTRAINT FK_9474526CCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9474526CCCFA12B8 ON comment (profile_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP FOREIGN KEY FK_9474526CCCFA12B8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9474526CCCFA12B8 ON comment
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comment DROP profile_id
        SQL);
    }
}
