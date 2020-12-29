<?php

use yii\db\Migration;

/**
 * Class m201104_111454_create_fk_all
 */
class m201104_111454_create_fk_all extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //wallet keys
        $this->addForeignKey(
            'fk_wallet_user',
            'wallet',
            'id_user',
            'user',
            'id',
            'RESTRICT'
        );

        //transfer keys

        $this->addForeignKey(
            'fk_transfer_sender_wallet',
            'transfer',
            'id_sender_wallet',
            'wallet',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk_transfer_recipient_wallet',
            'transfer',
            'id_recipient_wallet',
            'wallet',
            'id',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk_transfer_status',
            'transfer',
            'id_status',
            'transfer_status',
            'id',
            'RESTRICT'
        );

        //end of transfer keys
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_wallet_user', 'wallet');
        $this->dropForeignKey('fk_transfer_sender_wallet', 'transfer');
        $this->dropForeignKey('fk_transfer_recipient_wallet', 'transfer');
        $this->dropForeignKey('fk_transfer_status', 'transfer');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_111454_create_fk_all cannot be reverted.\n";

        return false;
    }
    */
}
