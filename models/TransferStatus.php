<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "transfer_status".
 *
 * @property int $id
 * @property string $title
 *
 * @property Transfer[] $transfers
 */
class TransferStatus extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transfer_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Status',
        ];
    }

    /**
     * Gets query for [[Transfers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransfers()
    {
        return $this->hasMany(Transfer::class, ['id_status' => 'id']);
    }

    public static function getIdByTitle(string $title) {
        return self::findOne([
           'title' => $title,
        ])['id'];
    }
}
