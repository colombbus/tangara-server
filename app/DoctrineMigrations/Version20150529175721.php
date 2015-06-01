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
        // perform nothing: ACL init has to be done through symfony command init:acl
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // remove Project ACL class
        $this->addSql("DELETE FROM acl_classes");
        $this->addSql("DELETE FROM acl_security_identities");
        $this->addSql("DELETE FROM acl_object_identities");
        $this->addSql("DELETE FROM acl_object_identity_ancestors");
        $this->addSql("DELETE FROM acl_entries");
    }
    
    public function postUp(Schema $schema) {
        // insert project class
        $this->connection->insert("acl_classes", ['class_type'=>"Tangara\\CoreBundle\\Entity\\Project"]);
        $classId = $this->connection->lastInsertId();
        // scan users
        $result = $this->connection->query("SELECT username, home_id from users");
        while ($row = $result->fetch()) {
            $hId = $row['home_id'];
            if (isset($hId)) {
                // insert User object
                $class = "Tangara\\CoreBundle\\Entity\\User-".$row['username'];
                $this->connection->insert("acl_security_identities", ['identifier'=>$class, 'username'=>1]);
                //$this->connection->query("INSERT INTO acl_security_identities (identifier, username) VALUES (\"$class\", 1);");
                $securityId = $this->connection->lastInsertId();
                // insert Project object
                $this->connection->insert("acl_object_identities", ['class_id'=>$classId, 'object_identifier'=>$hId, 'entries_inheriting'=>1]);
                $objectId = $this->connection->lastInsertId();
                //$this->connection->query("INSERT INTO acl_object_identity_ancestors (object_identity_id, ancestor_id) VALUES ($objectId, $objectId);");
                $this->connection->insert("acl_object_identity_ancestors", ['object_identity_id'=>$objectId, 'ancestor_id'=>$objectId]);
                // insert entry
                $this->connection->insert("acl_entries", ['class_id'=>$classId, 'object_identity_id'=>$objectId, 'security_identity_id'=>$securityId, 'ace_order'=>0, 'mask'=>128, 'granting'=>1, 'granting_strategy'=>'all', 'audit_success'=>0, 'audit_failure'=>0]);
            }
        }
    }
}
