<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m201104_104906_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(45)->notNull(),
            'last_name' => $this->string(45)->notNull(),
            'gender' => $this->tinyInteger(1),
            'login' => $this->string(60)->unique()->notNull(),
            'email' => $this->string(60)->unique()->notNull(),
            'password' => $this->string(200)->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
