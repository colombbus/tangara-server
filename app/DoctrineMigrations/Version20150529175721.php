<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150529175721 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // add Project ACL class
        $this->addSql("INSERT INTO `acl_classes` (`id`, `class_type`) VALUES (NULL, 'Tangara\CoreBundle\Entity\Project');");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // remove Project ACL class
        $this->addSql("DELETE FROM `acl_classes` WHERE `id` = 1");

    }
    
    public function postUp(Schema $schema) {
        $result = $this->connection->query("SELECT username, home_id from users");
        while ($row = $result->fetch()) {
            // insert User object
            $class = "Tangara\Core\Entity\User-".$row['username'];
            $result2 = $this->connection->query("INSERT INTO `acl_object_identities` (`class_id`, `object_identifier`, `entries_inheriting`) VALUES (NULL, $hId, 1);");
            // insert Project object
            $hId = $row['home_id'];
            $this->connection->query("INSERT INTO `acl_object_identities` (`class_id`, `object_identifier`, `entries_inheriting`) VALUES (NULL, $hId, 1);");
            $this->connection->query("INSERT INTO `acl_object_identities` (`class_id`, `object_identifier`, `entries_inheriting`) VALUES (NULL, $id, 1);");
        }
    }
}
