<?php


namespace app\models;


use yii\base\Model;

class WalletForm extends Model
{
    public $title;
    public $amount;

    public function rules() {
        return [
            [['title', 'amount'], 'required'],
            ['amount', 'number', 'min' => 0],
        ];
    }
}