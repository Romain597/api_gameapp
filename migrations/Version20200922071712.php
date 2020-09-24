<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200922071712 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_category DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE game_category ADD PRIMARY KEY (category_id, game_id)');
        $this->addSql('ALTER TABLE game_studio DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE game_studio ADD PRIMARY KEY (studio_id, game_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_category DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE game_category ADD PRIMARY KEY (game_id, category_id)');
        $this->addSql('ALTER TABLE game_studio DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE game_studio ADD PRIMARY KEY (game_id, studio_id)');
    }
}
