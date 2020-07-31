<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * KunaDealsSearch represents the model behind the search form about `\common\models\KunaDeals`.
 */
class KunaDealsSearch extends KunaDeals
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'status', 'bank'], 'safe'],
            [['amount', 'percent', 'price'], 'number'],
            [['executed'], 'boolean'],
            [['created_at', 'updated_at'], 'integer'],
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
        $query = KunaDeals::find()->orderBy('created_at DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'amount' => $this->amount,
            'percent' => $this->percent,
            'price' => $this->price,
            'status' => $this->status,
            'executed' => $this->executed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'order_id', $this->order_id])
            ->andFilterWhere(['like', 'bank', $this->bank]);

        return $dataProvider;
    }
}
