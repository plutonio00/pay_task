<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transfer_status}}`.
 */
class m201104_105510_create_transfer_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transfer_status}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(45)->unique()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transfer_status}}');
    }
}
