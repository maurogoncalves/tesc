<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\HistoricoVeiculo;

/**
 * HistoricoVeiculoSearch represents the model behind the search form about `common\models\HistoricoVeiculo`.
 */
class HistoricoVeiculoSearch extends HistoricoVeiculo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idVeiculo', 'idCondutor', 'lat', 'lng'], 'integer'],
            [['data'], 'safe'],
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
        $query = HistoricoVeiculo::find();

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
            'data' => $this->data,
            'idVeiculo' => $this->idVeiculo,
            'idCondutor' => $this->idCondutor,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ]);

        return $dataProvider;
    }
}
