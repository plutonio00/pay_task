<?php

namespace app\models;

use app\exceptions\TransferStatusNotFoundException;
use app\traits\ValidateTrait;
use app\utils\NumberFormatUtils;
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
 * @property TransferStatus $status
 */
class Transfer extends ActiveRecord
{
    use ValidateTrait;

    protected const SENDER_TYPE = 'sender';
    protected const RECIPIENT_TYPE = 'recipient';
    public const EXEC_TIME_FORMAT = 'd.m.Y H:00';
    public const TIMESTAMP_FORMAT = 'Y-m-d H:00:00';
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
                    return date(self::TIMESTAMP_FORMAT, strtotime($model->sender->exec_time));
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
            [
                ['id_sender_wallet'], 'exist', 'skipOnError' => false,
                'targetClass' => Wallet::class, 'targetAttribute' => ['id_sender_wallet' => 'id'],
                'message' => self::WALLET_DOES_NOT_EXIST,
            ],
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
            [['amount', 'exec_time'], 'validateField'],
            [['id_sender_wallet'], 'validateField', 'params' => ['method' => 'validateWallet', 'args' => ['type' => self::SENDER_TYPE]]],
            [['id_recipient_wallet'], 'validateField', 'params' => ['method' => 'validateWallet', 'args' => ['type' => self::RECIPIENT_TYPE]]],
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
            'id_sender_wallet' => 'Sender\'s wallet',
            'id_recipient' => 'Id Recipient',
            'id_recipient_wallet' => 'Recipient\'s wallet',
            'amount' => 'Amount',
            'exec_time' => 'Execute time',
            'id_status' => 'Id status',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
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

    public static function getTransfersForExecute()
    {
        return self::getTransfers(['recipientWallet', 'senderWallet'])
            ->where([
                'and',
                ['id_status' => TransferStatus::getIdByTitle(TransferStatus::IN_PROGRESS)],
                'exec_time <= NOW()'
            ])
            ->all();
    }

    /**
     * @param int $idUser
     * @return ActiveQuery
     * @throws TransferStatusNotFoundException
     */
    public static function getTransfersForUser(int $idUser): ActiveQuery
    {
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

    public function getDisplayWalletDataForOwner($walletType)
    {
        $wallet = $this->{$walletType . 'Wallet'};
        return $wallet->id_user === Yii::$app->user->getId() ? $wallet->title : $wallet->id;
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
            if (DateTime::createFromFormat(self::TIMESTAMP_FORMAT, $this->exec_time)) {
                return;
            }

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
                $now->format(self::EXEC_TIME_FORMAT)
            ));
            return;
        }
    }

    public function validateAmount(string $attribute): void
    {
        $idStatusInProgress = TransferStatus::getIdByTitle(TransferStatus::IN_PROGRESS);
        $sumAmountTransfers = self::find()
            ->where([
                'id_sender' => Yii::$app->user->getId(),
                'id_sender_wallet' => $this->id_sender_wallet,
                'id_status' => $idStatusInProgress
            ])
            ->sum('amount');

        $sumAmountTransfers += $this->amount;

        /**
         * @var Wallet $senderWallet
         */
        $senderWallet = Wallet::findOne([
            'id' => $this->id_sender_wallet,
        ]);

        if ($sumAmountTransfers > $senderWallet->amount) {
            $this->addError($attribute, sprintf(
                'The amount of transfers that you have planned exceeds the amount on the wallet "%s".
                Remaining balance after completing scheduled transfers: %s',
                $senderWallet->title,
                NumberFormatUtils::formatAmount($senderWallet->amount - $sumAmountTransfers),
            ));
            return;
        }
    }
}
