<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ReciboPagamentoAutonomo;

/**
 * ReciboPagamentoAutonomoSearch represents the model behind the search form about `common\models\ReciboPagamentoAutonomo`.
 */
class ReciboPagamentoAutonomoSearch extends ReciboPagamentoAutonomo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idCondutor', 'data', 'numRecibo', 'mes', 'ano'], 'safe'],
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
        $query = ReciboPagamentoAutonomo::find();

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

        if (Usuario::permissao(Usuario::PERFIL_CONDUTOR)) {
            $query->joinWith('condutor');
            $query->andFilterWhere(['=', 'Condutor.idUsuario', \Yii::$app->User->identity->id]);
        }

        if (!empty($this->data)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->data);
            $query->andFilterWhere(['=', 'data', $date->format('Y-m-d')]);
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'idCondutor' => $this->idCondutor,
            'numRecibo' => $this->numRecibo,
            'mes' => $this->mes,
            'ano' => $this->ano
            // 'data' => $this->data,
        ]);

        return $dataProvider;
    }
}
