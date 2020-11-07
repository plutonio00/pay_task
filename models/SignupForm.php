<?php

namespace app\models;

use yii\base\Model;

class SignupForm extends Model
{
    public $first_name;
    public $last_name;
    public $is_male;
    public $login;
    public $email;
    public $password;
    public $confirm_password;

    public function attributeLabels()
    {
        return [
            'is_male' => 'Gender',
        ];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['login', 'email', 'first_name', 'last_name', 'is_male', 'password', 'confirm_password'], 'required'],
            ['email', 'email'],
            [['login'], 'unique', 'targetClass' => User::class, 'message' => 'This username has already been taken.'],
            [['email'], 'unique', 'targetClass' => User::class, 'message' => 'This email has already been taken.'],
            ['password', 'compare', 'compareAttribute' => 'confirm_password'],
        ];
    }
}