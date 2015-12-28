<?php

use yii\db\mysql\Schema;

class m150816_130733_changelog extends yii\db\Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%changelog}}', [
            'id' => Schema::TYPE_PK,
            'action' => Schema::TYPE_INTEGER,
            'entity_type' => Schema::TYPE_STRING,
            'entity_id' => Schema::TYPE_STRING,
            'entity' => Schema::TYPE_STRING,
            'changes' => Schema::TYPE_BINARY,
            'env' => Schema::TYPE_BINARY,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->createIndex('idx_entity', '{{%changelog}}', ['entity_type', 'entity_id']);
        $this->createIndex('idx_created', '{{%changelog}}', ['created_at']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%changelog}}');
    }
}