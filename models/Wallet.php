<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use app\traits\ValidateTrait;

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
    use ValidateTrait;

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
                ['amount'], 'number', 'numberPattern' => Constants::AMOUNT_PATTERN,
                'message' => Constants::INVALID_AMOUNT_MESSAGE,
            ],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 200],
            [['title'], 'validateField'],
            [['id_user'], 'exist', 'skipOnError' => false, 'targetClass' => User::class, 'targetAttribute' => ['id_user' => 'id']],
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
     * @return ActiveQuery
     */
    public function getTransferSenderWallets(): ActiveQuery
    {
        return $this->hasMany(Transfer::class, ['id_sender_wallet' => 'id']);
    }

    /**
     * Gets query for [[TransferRecipientWallets]].
     *
     * @return ActiveQuery
     */
    public function getTransferRecipientWallets(): ActiveQuery
    {
        return $this->hasMany(Transfer::class, ['id_recipient_wallet' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'id_user']);
    }

    public function validateTitle(string $attribute): void
    {
        $wallet = self::findOne([
            'title' => $this->title,
            'id_user' => Yii::$app->user->getId(),
        ]);

        if ($wallet) {
            $this->addError($attribute, 'You already have wallet with such title. Choose another title');
            return;
        }
    }
}
