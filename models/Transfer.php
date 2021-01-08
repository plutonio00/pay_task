<?php

namespace app\models;

use DateTime;
use Exception;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "transfer".
 *
 * @property int $id
 * @property int $id_sender
 * @property int $id_recipient
 * @property int $id_sender_wallet
 * @property int $id_recipient_wallet
 * @property float $amount
 * @property string $exec_time
 * @property int $id_status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $sender
 * @property User $recipient
 * @property Wallet $senderWallet
 * @property Wallet $recipientWallet
 * @property Wallet[] $wallets
 * @property TransferStatus $status
 */
class Transfer extends ActiveRecord
{
    protected const SENDER_TYPE = 'sender';
    protected const EXEC_TIME_FORMAT = 'd.m.Y H:i';
    protected const WALLET_DOES_NOT_EXIST = 'Wallet with such id doesn\'t exist';
    public const FIELDS_FOR_FORM_VALIDATION = [
        'id_sender_wallet', 'id_recipient_wallet', 'amount', 'exec_time'
    ];

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'exec_time',
                    ActiveRecord::EVENT_AFTER_UPDATE => 'exec_time',
                ],
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', strtotime($model->sender->exec_time));
                }
            ]
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
            [['id_sender', 'id_sender_wallet', 'id_recipient', 'id_recipient_wallet', 'amount', 'exec_time', 'id_status'], 'required'],
            [['id_sender', 'id_sender_wallet', 'id_recipient', 'id_recipient_wallet', 'id_status'], 'integer'],
            [['id_sender'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_sender' => 'id']],
            [['id_recipient'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_recipient' => 'id']],
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
            'id_sender' => 'Id Sender',
            'id_sender_wallet' => 'Sender wallet',
            'id_recipient' => 'Id Recipient',
            'id_recipient_wallet' => 'Recipient\'s wallet id',
            'amount' => 'Amount',
            'exec_time' => 'Exec Time',
            'id_status' => 'Id Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Sender]].
     *
     * @return ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::class, ['id' => 'id_sender']);
    }

    /**
     * Gets query for [[Recipient]].
     *
     * @return ActiveQuery
     */
    public function getRecipient()
    {
        return $this->hasOne(User::class, ['id' => 'id_recipient']);
    }

    /**
     * Gets query for [[SenderWallet]].
     *
     * @return ActiveQuery
     */
    public function getSenderWallet()
    {
        return $this->hasOne(Wallet::class, ['id' => 'id_sender_wallet']);
    }

    /**
     * Gets query for [[RecipientWallet]].
     *
     * @return ActiveQuery
     */
    public function getRecipientWallet()
    {
        return $this->hasOne(Wallet::class, ['id' => 'id_recipient_wallet']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(TransferStatus::class, ['id' => 'id_status']);
    }

    public static function getTransfers(array $joinTables): ActiveQuery
    {
        return self::find()
            ->innerJoinWith($joinTables);
    }

    public static function getTransfersForUser(int $idUser) {
        $idStatusDone = TransferStatus::getIdByTitle(TransferStatus::DONE);

        return self::getTransfers(['recipientWallet', 'senderWallet', 'status'])
            ->where(['id_sender' => $idUser])
            ->orWhere([
                'and',
                ['id_recipient' => $idUser],
                ['id_status' => $idStatusDone],
                'id_sender <> id_recipient'
            ]);
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
