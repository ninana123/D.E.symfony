<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241117133631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_user_networks (id UUID NOT NULL, user_id VARCHAR(255) NOT NULL, network VARCHAR(255) NOT NULL, identity VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D7BAFD7BA76ED395 ON user_user_networks (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D7BAFD7B608487BC6A95E9C4 ON user_user_networks (network, identity)');
        $this->addSql('CREATE TABLE user_users (id VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, password_hash VARCHAR(255) DEFAULT NULL, confirm_token VARCHAR(255) DEFAULT NULL, status VARCHAR(16) NOT NULL, role VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, reset_token_token VARCHAR(255) DEFAULT NULL, reset_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB1E7927C74 ON user_users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB186EC69F0 ON user_users (reset_token_token)');
        $this->addSql('COMMENT ON COLUMN user_users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_users.reset_token_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE user_user_networks ADD CONSTRAINT FK_D7BAFD7BA76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_user_networks DROP CONSTRAINT FK_D7BAFD7BA76ED395');
        $this->addSql('DROP TABLE user_user_networks');
        $this->addSql('DROP TABLE user_users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
