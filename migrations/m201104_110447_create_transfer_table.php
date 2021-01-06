<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transfer}}`.
 */
class m201104_110447_create_transfer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transfer}}', [
            'id' => $this->primaryKey(),
            'id_sender' => $this->integer()->notNull(),
            'id_sender_wallet' => $this->integer()->notNull(),
            'id_recipient' => $this->integer()->notNull(),
            'id_recipient_wallet' => $this->integer()->notNull(),
            'amount' => $this->decimal(11, 2)->notNull(),
            'exec_time' => $this->timestamp(),
            'id_status' => $this->integer(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transfer}}');
    }
}
