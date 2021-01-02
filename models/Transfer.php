<?php

namespace app\models;

use DateTime;
use Exception;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "transfer".
 *
 * @property int $id
 * @property int $id_sender_wallet
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
    protected const SENDER_TYPE = 'sender';
    protected const EXEC_TIME_FORMAT = 'd.m.Y H:i';
    protected const WALLET_DOES_NOT_EXIST = 'Wallet with such id doesn\'t exist';

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
            [['id_sender_wallet', 'id_recipient_wallet', 'amount', 'exec_time', 'id_status'], 'required'],
            [['id_sender_wallet', 'id_recipient_wallet', 'id_status'], 'integer'],
            [
                ['amount'], 'number', 'numberPattern' => Constants::AMOUNT_PATTERN,
                'message' => Constants::INVALID_AMOUNT_MESSAGE,
            ],
            [['created_at', 'updated_at'], 'safe'],
            ['exec_time', 'validateExecTime'],
            [
                ['id_sender_wallet'], 'exist', 'skipOnError' => false,
                'targetClass' => Wallet::class, 'targetAttribute' => ['id_sender_wallet' => 'id'],
                'message' => self::WALLET_DOES_NOT_EXIST,
            ],
            [['id_sender_wallet'], 'validateWallet', 'params' => ['type' => self::SENDER_TYPE]],
            [
                ['id_recipient_wallet'], 'exist', 'skipOnError' => false,
                'targetClass' => Wallet::class,
                'targetAttribute' => ['id_recipient_wallet' => 'id'],
                'message' => self::WALLET_DOES_NOT_EXIST,
            ],
            ['id_recipient_wallet', 'compare', 'compareAttribute' => 'id_sender_wallet',
                'operator' => '!==',
                'message' => 'You must choose different wallets!'
            ],
            [['id_recipient_wallet'], 'validateWallet', 'params' => ['type' => 'recipient']],
            [['id_status'], 'exist', 'skipOnError' => false, 'targetClass' => TransferStatus::class, 'targetAttribute' => ['id_status' => 'id']],
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

    public function validateWallet(string $attribute, array $params): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $type = $params['type'];
        $wallet = Wallet::findOne([
            'id' => $this->{sprintf('id_%s_wallet', $type)},
        ]);

        if (!$wallet) {
            $this->addError($attribute, 'Such wallet doesn\'t exist');
            return;
        }

        if ($type === self::SENDER_TYPE && $wallet->id_user !== Yii::$app->user->getId()) {
            $this->addError($attribute, 'You cannot make a transfer from someone else\'s wallet');
            return;
        }
    }

    public function validateExecTime(string $attribute): void
    {
        try {
            $datetime = DateTime::createFromFormat(self::EXEC_TIME_FORMAT, $this->exec_time);
        } catch (Exception $e) {
            $this->addError($attribute, 'Unexpected problem! Try again.');
            return;
        }

        if (!$datetime) {
            $this->addError($attribute, 'Incorrect time format! Use the calendar on the form to select the date.');
            return;
        }

        $now = new DateTime();

        if ($datetime < $now) {
            $this->addError($attribute, sprintf(
                    'You need to select a date and time later than %s',
                    $now->format(self::EXEC_TIME_FORMAT))
            );
            return;
        }
    }
}
