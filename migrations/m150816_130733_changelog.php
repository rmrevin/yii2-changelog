<?php

class m150816_130733_changelog extends yii\db\Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=ARCHIVE';
        }

        $this->createTable('{{%changelog}}', [
            'id' => $this->primaryKey(),
            'action' => $this->integer(),
            'entity_type' => $this->string(),
            'entity_id' => $this->string(),
            'present' => $this->string(),
            'changes' => $this->binary(),
            'env' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx_entity', '{{%changelog}}', ['entity_type', 'entity_id']);
        $this->createIndex('idx_created', '{{%changelog}}', ['created_at']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%changelog}}');
    }
}
