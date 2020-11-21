<?php


namespace app\models;


use yii\base\Model;

class ReplenishForm extends Model
{
    public $id_wallet;
    public $amount;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id_wallet', 'amount'], 'required'],
            ['id_wallet', 'integer'],
            [
                ['amount'], 'number', 'numberPattern' => Constants::AMOUNT_PATTERN,
                'message' => Constants::INVALID_AMOUNT_MESSAGE,
            ],
        ];
    }
}