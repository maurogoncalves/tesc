<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AlunoRota;

/**
 * AlunoRotaSearch represents the model behind the search form about `common\models\AlunoRota`.
 */
class AlunoRotaSearch extends AlunoRota
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idCondutorRota', 'idAluno', 'idEscola'], 'integer'],
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
        $query = AlunoRota::find();

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
            'idCondutorRota' => $this->idCondutorRota,
            'idAluno' => $this->idAluno,
            'idEscola' => $this->idEscola,
        ]);

        return $dataProvider;
    }
}
