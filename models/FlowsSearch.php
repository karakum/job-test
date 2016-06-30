<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Flows;

/**
 * FlowsSearch represents the model behind the search form about `app\models\Flows`.
 */
class FlowsSearch extends Flows
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'operation_id'], 'integer'],
            [['begin', 'debit', 'credit', 'end'], 'number'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Flows::find();

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
            'user_id' => $this->user_id,
            'operation_id' => $this->operation_id,
            'begin' => $this->begin,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'end' => $this->end,
            'datetime' => $this->datetime,
        ]);

        return $dataProvider;
    }
}
