<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wallet}}`.
 */
class m201104_110025_create_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%wallet}}', [
            'id' => $this->primaryKey(),
            'id_user' => $this->integer()->notNull(),
            'title' => $this->string(200)->notNull(),
            'amount' => $this->decimal(11, 2)->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%wallet}}');
    }
}
