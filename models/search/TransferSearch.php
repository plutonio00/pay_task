<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transfer;

/**
 * TransferSearch represents the model behind the search form of `app\models\Transfer`.
 */
class TransferSearch extends Transfer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_sender', 'id_sender_wallet', 'id_recipient', 'id_recipient_wallet', 'id_status'], 'integer'],
            [['amount'], 'number'],
            [['exec_time', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Transfer::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'id_sender' => $this->id_sender,
            'id_sender_wallet' => $this->id_sender_wallet,
            'id_recipient' => $this->id_recipient,
            'id_recipient_wallet' => $this->id_recipient_wallet,
            'amount' => $this->amount,
            'exec_time' => $this->exec_time,
            'id_status' => $this->id_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
