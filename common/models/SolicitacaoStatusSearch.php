<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SolicitacaoStatus;

/**
 * SolicitacaoStatusSearch represents the model behind the search form about `common\models\SolicitacaoStatus`.
 */
class SolicitacaoStatusSearch extends SolicitacaoStatus
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idSolicitacaoTransporte', 'idUsuario'], 'integer'],
            [['justificativa'], 'safe'],
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
        $query = SolicitacaoStatus::find();

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
            'idSolicitacaoTransporte' => $this->idSolicitacaoTransporte,
            'idUsuario' => $this->idUsuario,
        ]);

        $query->andFilterWhere(['like', 'justificativa', $this->justificativa]);

        return $dataProvider;
    }
}
