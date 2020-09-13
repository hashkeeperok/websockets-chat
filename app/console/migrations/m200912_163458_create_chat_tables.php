<?php

use yii\db\Migration;

/**
 * Class m200912_163458_create_chat_tables
 */
class m200912_163458_create_chat_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('chat_message', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'text' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('chat_message');
    }
}
