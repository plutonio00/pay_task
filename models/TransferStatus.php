<?php

namespace app\models;

use app\exceptions\TransferStatusNotFoundException;
use yii\db\ActiveQuery;
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
    public const IN_PROGRESS = 'in progress';
    public const DONE = 'done';

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
     * @return ActiveQuery
     */
    public function getTransfers(): ActiveQuery
    {
        return $this->hasMany(Transfer::class, ['id_status' => 'id']);
    }

    public static function getIdByTitle(string $title): int
    {
        $statusId = self::findOne([
           'title' => $title,
        ])['id'];

        if (!$statusId) {
            throw new TransferStatusNotFoundException(
                sprintf('Status \'%s\' not found in database', $title)
            );
        }

        return $statusId;
    }
}
