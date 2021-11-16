<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CondutorEscola;

/**
 * CondutorEscolaSearch represents the model behind the search form about `common\models\CondutorEscola`.
 */
class CondutorEscolaSearch extends CondutorEscola
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idCondutor', 'idEscola'], 'integer'],
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
        $query = CondutorEscola::find();

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
            'idEscola' => $this->idEscola,
        ]);

        return $dataProvider;
    }
}
