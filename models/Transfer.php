<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "transfer".
 *
 * @property int $id
 * @property int $id_sender
 * @property int $id_sender_wallet
 * @property int $id_recipient
 * @property int $id_recipient_wallet
 * @property float $amount
 * @property string $exec_time
 * @property int $id_status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Wallet $senderWallet
 * @property Wallet $recipientWallet
 * @property TransferStatus $status
 */
class Transfer extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transfer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sender', 'id_sender_wallet', 'id_recipient', 'id_recipient_wallet', 'amount', 'exec_time', 'id_status', 'created_at', 'updated_at'], 'required'],
            [['id_sender', 'id_sender_wallet', 'id_recipient', 'id_recipient_wallet', 'id_status'], 'integer'],
            [
                ['amount'], 'number', 'numberPattern' => Constants::AMOUNT_PATTERN,
                'message' => Constants::INVALID_AMOUNT_MESSAGE,
            ],
            [['exec_time', 'created_at', 'updated_at'], 'safe'],
            [['id_sender_wallet'], 'exist', 'skipOnError' => true, 'targetClass' => Wallet::class, 'targetAttribute' => ['id_sender_wallet' => 'id']],
            [['id_sender_wallet'], 'validateIdSenderWallet'],
            [['id_recipient_wallet'], 'exist', 'skipOnError' => true, 'targetClass' => Wallet::class, 'targetAttribute' => ['id_recipient_wallet' => 'id']],
            [['id_status'], 'exist', 'skipOnError' => true, 'targetClass' => TransferStatus::class, 'targetAttribute' => ['id_status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_sender_wallet' => 'Your wallet',
            'id_recipient_wallet' => 'Recipient\'s wallet id',
            'amount' => 'Amount',
            'exec_time' => 'Exec Time',
            'id_status' => 'Id Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[SenderWallet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSenderWallet()
    {
        return $this->hasOne(Wallet::class, ['id' => 'id_sender_wallet']);
    }

    /**
     * Gets query for [[RecipientWallet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecipientWallet()
    {
        return $this->hasOne(Wallet::class, ['id' => 'id_recipient_wallet']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(TransferStatus::class, ['id' => 'id_status']);
    }

    public function validateIdSenderWallet(string $attribute): void {
        if (!$this->hasErrors()) {
            $wallet = Wallet::findOne([
                'id_user' => $this->id_sender,
                'id' => $this->id_sender_wallet,
            ]);

            if (!$wallet) {
                $this->addError($attribute, 'You haven\'t such wallet');
            }
        }
    }

    public function validateIdRecipientWallet(string $attribute): void {
        if (!$this->hasErrors()) {
            $wallet = Wallet::findOne([
                'id' => $this->id_sender_wallet,
            ]);

            if (!$wallet) {
                $this->addError($attribute, 'Such wallet doesn\'t exist');
            }
        }
    }

    public function validateExecTime(string $attribute) {
        if (!$this->hasErrors()) {

        }
    }
}
