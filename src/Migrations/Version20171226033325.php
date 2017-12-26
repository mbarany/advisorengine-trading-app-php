<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171226033325 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE "stock_transaction" (id UUID NOT NULL, user_id UUID NOT NULL, symbol VARCHAR(10) NOT NULL, amount NUMERIC(7, 2) NOT NULL, price NUMERIC(7, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ADF9A3E5A76ED395 ON "stock_transaction" (user_id)');
        $this->addSql('ALTER TABLE "stock_transaction" ADD CONSTRAINT FK_ADF9A3E5A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C912ED9D ON "user" (api_key)');
        $this->addSql('ALTER INDEX uniq_ac64a0baf85e0677 RENAME TO UNIQ_8D93D649F85E0677');
        $this->addSql('ALTER INDEX uniq_ac64a0bae7927c74 RENAME TO UNIQ_8D93D649E7927C74');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE "stock_transaction"');
        $this->addSql('DROP INDEX UNIQ_8D93D649C912ED9D');
        $this->addSql('ALTER INDEX uniq_8d93d649e7927c74 RENAME TO uniq_ac64a0bae7927c74');
        $this->addSql('ALTER INDEX uniq_8d93d649f85e0677 RENAME TO uniq_ac64a0baf85e0677');
    }
}
