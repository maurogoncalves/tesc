<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use common\models\Condutor;

/**
 * CondutorSearch represents the model behind the search form about `common\models\Condutor`.
 */
class CondutorSearch extends Condutor
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idUsuario', 'alvara', 'inscricaoMunicipal', 'cnhRegistro', 'tipoContrato', 'valorPagoKmViagem', 'idCNHCondutor', 'idComprovanteEndereco', 'idCRLV', 'idVistoriaEstadual', 'idVstoriaMunicipal', 'idApoliceSeguro', 'idContrato','status'], 'integer'],
            [['dataNascimento', 'cpf', 'nit','cep', 'endereco', 'bairro', 'telefone', 'email', 'cnhValidade', 'dataInicioContrato','nome','dataFimContrato','regiao','capacidadeVeiculoCondutor','veiculoAdaptadoCondutor','rg', 'idCondutor','ano','mes','diasTrabalhados','sabadoLetivo','diasExcepcionais1','viagemKm1','diasExcepcionais2','viagemKm2','valorNota','protocoloTESC','protocoloGC','lote','saldoAF','alocacao', 'pendencias'], 'safe'],
            [['lat', 'lng'], 'number'],
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
        $query = Condutor::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        // $dataProvider = new SqlDataProvider([
        //     'sql' => $query->createCommand()->getRawSql(),           
        // ]);   

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        if ($this->idCondutor || $this->ano || $this->mes || $this->diasTrabalhados || $this->sabadoLetivo || $this->diasExcepcionais1 || $this->viagemKm1 || $this->diasExcepcionais2 || $this->viagemKm2 || $this->valorNota || $this->protocoloTESC || $this->protocoloGC || $this->lote || $this->saldoAF)
        {
            // $query->join('RIGHT JOIN', 'ControleFinanceiro', 'ControleFinanceiro.idCondutor=Condutor.id');
            $query->joinWith('controleFinanceiro', true);
        }

        if(Usuario::permissao(Usuario::PERFIL_CONDUTOR))
            $query->andFilterWhere(['=','idUsuario' ,\Yii::$app->User->identity->id]);
        
        if(!empty($this->dataInicioContrato)) {
            $date = \DateTime::createFromFormat( 'd/m/Y', $this->dataInicioContrato);
            $query->andFilterWhere(['>=', 'dataInicioContrato', $date->format('Y-m-d')]);
        }

        if(!empty($this->dataFimContrato)) {
            $date = \DateTime::createFromFormat( 'd/m/Y', $this->dataFimContrato);
            $query->andFilterWhere(['<=', 'dataFimContrato', $date->format('Y-m-d')]);
        }

        $query->leftJoin('Veiculo', 'Veiculo.idCondutor=Condutor.id');

        if($this->regiao)
        {
            $condutoresRegiao = CondutorRegiao::find()->select('idCondutor')->where(['regiao' => $this->regiao])->all();
            $idsCondutores = array_column($condutoresRegiao, 'idCondutor');
            $query->andFilterWhere(['in','Condutor.id', $idsCondutores]);

        }
        if($this->dataNascimento){
            $data = explode('- ', $this->dataNascimento);
       
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

            $data[0] = explode('/', $data[0]);
            $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(dataNascimento,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(dataNascimento,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'idUsuario' => $this->idUsuario,
            'status' => $this->status,
            //'idVeiculo' => $this->idVeiculo,
            // 'dataNascimento' => $this->dataNascimento,
            'alvara' => $this->alvara,
            'inscricaoMunicipal' => $this->inscricaoMunicipal,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'cnhRegistro' => $this->cnhRegistro,
            // 'regiao' => $this->regiao,
            'cnhValidade' => $this->cnhValidade,
            // 'dataInicioContrato' => $this->dataInicioContrato,
            // 'dataFimContrato' => $this->dataFimContrato,
            'tipoContrato' => $this->tipoContrato,
            'valorPagoKmViagem' => $this->valorPagoKmViagem,
            'idCNHCondutor' => $this->idCNHCondutor,
            'idComprovanteEndereco' => $this->idComprovanteEndereco,
            'idCRLV' => $this->idCRLV,
            'idVistoriaEstadual' => $this->idVistoriaEstadual,
            'idVstoriaMunicipal' => $this->idVstoriaMunicipal,
            'idApoliceSeguro' => $this->idApoliceSeguro,
            'idContrato' => $this->idContrato,
            'Veiculo.capacidade' => $this->capacidadeVeiculoCondutor,
            'Veiculo.adaptado' =>  $this->veiculoAdaptadoCondutor,
            'Veiculo.alocacao' =>  $this->alocacao,
            
            'ControleFinanceiro.idCondutor' => $this->idCondutor,
            'ControleFinanceiro.ano' => $this->ano,
            'ControleFinanceiro.mes' => $this->mes,
            'ControleFinanceiro.diasTrabalhados' => $this->diasTrabalhados,
            'ControleFinanceiro.sabadoLetivo' => $this->sabadoLetivo,
            'ControleFinanceiro.diasExcepcionais1' => $this->diasExcepcionais1,
            'ControleFinanceiro.viagemKm1' => $this->viagemKm1,
            'ControleFinanceiro.diasExcepcionais2' => $this->diasExcepcionais2,
            'ControleFinanceiro.viagemKm2' => $this->viagemKm2,
            'ControleFinanceiro.valorNota' => $this->valorNota,
            'ControleFinanceiro.protocoloTESC' => $this->protocoloTESC,
            'ControleFinanceiro.protocoloGC' => $this->protocoloGC,
            'ControleFinanceiro.lote' => $this->lote,
            'ControleFinanceiro.saldoAF' => $this->saldoAF

        ]);

        $query->andFilterWhere(['like', 'cpf', trim($this->cpf)])
            ->andFilterWhere(['like', 'nit', trim($this->nit)])
            ->andFilterWhere(['like', 'rg', trim($this->rg)])
            ->andFilterWhere(['like', 'cep', trim($this->cep)])
            ->andFilterWhere(['like', 'nome', trim($this->nome)])
            ->andFilterWhere(['like', 'endereco', trim($this->endereco)])
            ->andFilterWhere(['like', 'bairro', trim($this->bairro)])
            ->andFilterWhere(['like', 'telefone', trim($this->telefone)])
            ->andFilterWhere(['like', 'email', trim($this->email)])
            ->andFilterWhere(['like', 'pendencias', trim($this->pendencias)]);

        return $dataProvider;
    }
}
