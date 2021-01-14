<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property int $is_male
 * @property string $login
 * @property string $email
 * @property string $password
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Wallet[] $wallets
 * @property Transfer[] $recipientTransfers
 * @property Transfer[] $senderTransfers
 */
class User extends ActiveRecord implements IdentityInterface
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
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'is_male', 'login', 'email', 'password'], 'required'],
            [['is_male'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 45],
            [['login', 'email'], 'string', 'max' => 60],
            [['password'], 'string', 'max' => 200],
            [['login'], 'unique'],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'is_male' => 'Is Male',
            'login' => 'Login',
            'email' => 'Email',
            'password' => 'Password',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Wallets]].
     *
     * @return ActiveQuery
     */
    public function getWallets(): ActiveQuery
    {
        return $this->hasMany(Wallet::class, ['id_user' => 'id']);
    }

    public static function findByLoginOrEmail(string $attribute)
    {
        return static::find()
            ->where(['login' => $attribute])
            ->orWhere(['email' => $attribute])
            ->one();
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }

    /**
     * @return array
     */
    public function getWalletsArray(): array
    {
        $wallets = $this->getWallets()
            ->asArray()
            ->all()
        ;

        return ArrayHelper::map($wallets, 'id', 'title');
    }

    public static function getUsersAndWallets(): ActiveQuery
    {
        return static::find()
            ->with('wallets');
    }

    public static function getAccountInfo(string $login)
    {
        return static::getUsersAndWallets()
            ->with('wallets')
            ->where(['login' => $login])
            ->one();
    }

    /**
     * Gets query for [[RecipientTransfers]].
     *
     * @return ActiveQuery
     */
    public function getRecipientTransfers(): ActiveQuery
    {
        return $this->hasMany(Transfer::class, ['id_recipient' => 'id']);
    }

    /**
     * Gets query for [[SenderTransfers]].
     *
     * @return ActiveQuery
     */
    public function getSenderTransfers(): ActiveQuery
    {
        return $this->hasMany(Transfer::class, ['id_sender' => 'id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Token authentication is not supported!');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey(){}

    public function validateAuthKey($authKey){}

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}
