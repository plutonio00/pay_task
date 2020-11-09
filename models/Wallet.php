<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "wallet".
 *
 * @property int $id
 * @property int $id_user
 * @property string $title
 * @property float $amount
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Transfer[] $transferSenderWallets
 * @property Transfer[] $transferRecipientWallets
 * @property User $user
 */
class Wallet extends ActiveRecord
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
        return 'wallet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'title', 'amount'], 'required'],
            [['id_user'], 'integer'],
            [
                ['amount'], 'number', 'numberPattern' => '/^\d{1,11}(\.\d{1,2})?$/',
                'message' => 'Amount must be a decimal number with 1 to 11 digits and 1 to 2 optional decimal places. Separate the whole part from the fractional part with the symbol  \'.\''
            ],
            //[['amount'], 'max' => 13],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 200],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Id User',
            'title' => 'Title',
            'amount' => 'Amount',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
        ];
    }

    /**
     * Gets query for [[TransferSenderWallets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransferSenderWallets()
    {
        return $this->hasMany(Transfer::class, ['id_sender_wallet' => 'id']);
    }

    /**
     * Gets query for [[TransferRecipientWallets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransferRecipientWallets()
    {
        return $this->hasMany(Transfer::class, ['id_recipient_wallet' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'id_user']);
    }
}
