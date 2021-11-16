<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Log;

/**
 * LogSearch represents the model behind the search form about `common\models\Log`.
 */
class LogSearch extends Log
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'acao', 'idUsuario', 'idAlunoTable', 'idEscolaTable', 'idSolicitacaoTransporteTable', 'idSolicitacaoCreditoTable', 'idCondutorRotaTable', 'idOcorrenciaTable', 'idCondutorTable', 'idVeiculoTable', 'idMarcaTable', 'idModeloTable', 'idUsuarioTable', 'idJustificativaTable', 'idReciboPagamentoAutonomoTable', 'idNecessidadesEspeciaisTable', 'idConfiguracaoTable', 'idEmpresaTable'], 'integer'],
            [['data', 'referencia', 'tabela', 'coluna', 'antes', 'depois'], 'safe'],
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
        $query = Log::find();

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
            'acao' => $this->acao,
            'idUsuario' => $this->idUsuario,
            'idAlunoTable' => $this->idAlunoTable,
            'idEscolaTable' => $this->idEscolaTable,
            'idSolicitacaoTransporteTable' => $this->idSolicitacaoTransporteTable,
            'idSolicitacaoCreditoTable' => $this->idSolicitacaoCreditoTable,
            'idCondutorRotaTable' => $this->idCondutorRotaTable,
            'idOcorrenciaTable' => $this->idOcorrenciaTable,
            'idCondutorTable' => $this->idCondutorTable,
            'idVeiculoTable' => $this->idVeiculoTable,
            'idMarcaTable' => $this->idMarcaTable,
            'idModeloTable' => $this->idModeloTable,
            'idUsuarioTable' => $this->idUsuarioTable,
            'idJustificativaTable' => $this->idJustificativaTable,
            'idReciboPagamentoAutonomoTable' => $this->idReciboPagamentoAutonomoTable,
            'idNecessidadesEspeciaisTable' => $this->idNecessidadesEspeciaisTable,
            'idConfiguracaoTable' => $this->idConfiguracaoTable,
            'idEmpresaTable' => $this->idEmpresaTable,
        ]);

        $query->andFilterWhere(['like', 'referencia', $this->referencia])
            ->andFilterWhere(['like', 'tabela', $this->tabela])
            ->andFilterWhere(['like', 'coluna', $this->coluna])
            ->andFilterWhere(['like', 'antes', $this->antes])
            ->andFilterWhere(['like', 'depois', $this->depois]);

        return $dataProvider;
    }
}
