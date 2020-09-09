<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200827194153 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE episode (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE character_in_episodes (episode_id INT NOT NULL, star_wars_character_id INT NOT NULL, INDEX IDX_3E46882D362B62A0 (episode_id), INDEX IDX_3E46882DCBC9B4C4 (star_wars_character_id), PRIMARY KEY(episode_id, star_wars_character_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE character_in_episodes ADD CONSTRAINT FK_3E46882D362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE character_in_episodes ADD CONSTRAINT FK_3E46882DCBC9B4C4 FOREIGN KEY (star_wars_character_id) REFERENCES star_wars_character (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE star_wars_character DROP episodes');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE character_in_episodes DROP FOREIGN KEY FK_3E46882D362B62A0');
        $this->addSql('DROP TABLE episode');
        $this->addSql('DROP TABLE character_in_episodes');
        $this->addSql('ALTER TABLE star_wars_character ADD episodes VARCHAR(256) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
