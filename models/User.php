<?php

namespace app\models;

use Yii;

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
 * @property Transfer[] $transferRecipients
 * @property Transfer[] $transferSenders
 * @property Wallet[] $wallets
 */
class User extends \yii\db\ActiveRecord
{
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
            [['id', 'first_name', 'last_name', 'is_male', 'login', 'email', 'password'], 'required'],
            [['id', 'is_male'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 45],
            [['login', 'email'], 'string', 'max' => 60],
            [['password'], 'string', 'max' => 200],
            [['login'], 'unique'],
            [['email'], 'unique'],
            [['id'], 'unique'],
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
     * Gets query for [[Transfers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransferRecipients()
    {
        return $this->hasMany(Transfer::class, ['id_recipient' => 'id']);
    }

    /**
     * Gets query for [[Transfers0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransferSenders()
    {
        return $this->hasMany(Transfer::class, ['id_sender' => 'id']);
    }

    /**
     * Gets query for [[Wallets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWallets()
    {
        return $this->hasMany(Wallet::class, ['id_user' => 'id']);
    }
}
