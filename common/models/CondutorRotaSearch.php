<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CondutorRota;

/**
 * CondutorRotaSearch represents the model behind the search form about `common\models\CondutorRota`.
 */
class CondutorRotaSearch extends CondutorRota
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idCondutor', 'turno', 'sentido','viagem'], 'integer'],
            [['descricao', 'entrada', 'saida'], 'safe'],
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
        $query = CondutorRota::find();

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
            'idCondutor' => $this->idCondutor,
            'turno' => $this->turno,
            'sentido' => $this->sentido,
            'entrada' => $this->entrada,
            'saida' => $this->saida,
        ]);

        $query->andFilterWhere(['like', 'descricao', $this->descricao]);

        return $dataProvider;
    }
}
