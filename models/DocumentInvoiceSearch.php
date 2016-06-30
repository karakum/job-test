<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DocumentInvoice;

/**
 * DocumentInvoiceSearch represents the model behind the search form about `app\models\DocumentInvoice`.
 */
class DocumentInvoiceSearch extends DocumentInvoice
{

    public $includeIncome;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'payer_id', 'status'], 'integer'],
            [['comment', 'datetime'], 'safe'],
            [['value'], 'number'],
            [['includeIncome'], 'boolean'],
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
        $query = DocumentInvoice::find();

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

        if ($this->includeIncome && $this->user_id) {
            $query->andWhere([
                'or',
                ['user_id' => $this->user_id],
                ['payer_id' => $this->user_id],
            ]);

        } else {
            $query->andFilterWhere([
                'user_id' => $this->user_id,
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'payer_id' => $this->payer_id,
            'value' => $this->value,
            'datetime' => $this->datetime,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
