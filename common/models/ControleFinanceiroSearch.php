<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Condutor;

/**
 * CondutorSearch represents the model behind the search form about `common\models\Condutor`.
 */
class ControleFinanceiroSearch extends ControleFinanceiro
{
    public $cpf;
    public $nit;
    public $rg;
    public $cep;
    public $nome;
    public $endereco;
    public $bairro;
    public $telefone;
    public $email;
    public $alocacao;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['diasTrabalhados','sabadoLetivo','diasExcepcionais1','viagemKm1','diasExcepcionais2','viagemKm2','lote'], 'integer'],
            [['idCondutor','ano','mes','diasTrabalhados','sabadoLetivo','diasExcepcionais1','viagemKm1','diasExcepcionais2','viagemKm2','valorNota','protocoloTESC','protocoloGC','lote','saldoAF','cpf', 'nit','cep', 'endereco', 'bairro', 'telefone', 'email','nome','rg'], 'safe'],
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
        $query = ControleFinanceiro::find();
        $query->joinWith('condutor');
        $query->joinWith('condutor.veiculo');

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
        
        // if(!empty($this->condutor->dataInicioContrato)) {
        //     $date = \DateTime::createFromFormat( 'd/m/Y', $this->dataInicioContrato);
        //     $query->andFilterWhere(['>=', 'dataInicioContrato', $date->format('Y-m-d')]);
        // }

        // if(!empty($this->condutor->dataFimContrato)) {
        //     $date = \DateTime::createFromFormat( 'd/m/Y', $this->dataFimContrato);
        //     $query->andFilterWhere(['<=', 'dataFimContrato', $date->format('Y-m-d')]);
        // }

        // if($this->condutor->dataNascimento){
        //     $data = explode('- ', $this->condutor->dataNascimento);
       
        //     $data[1] = explode('/', $data[1]);
        //     $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

        //     $data[0] = explode('/', $data[0]);
        //     $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
        //     $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(dataNascimento,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
        //     $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(dataNascimento,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );
        // }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'idCondutor' => $this->idCondutor,
            'ano' => $this->ano,
            'mes' => $this->mes,
            'Veiculo.alocacao' => $this->alocacao,
            'diasTrabalhados' => $this->diasTrabalhados,
            'sabadoLetivo' => $this->sabadoLetivo,
            'diasExcepcionais1' => $this->diasExcepcionais1,
            'viagemKm1' => $this->viagemKm1,
            'diasExcepcionais2' => $this->diasExcepcionais2,
            'viagemKm2' => $this->viagemKm2,
            'valorNota' => $this->valorNota,
            'protocoloTESC' => $this->protocoloTESC,
            'protocoloGC' => $this->protocoloGC,
            'lote' => $this->lote,
            'saldoAF' => $this->saldoAF

        ]);

        $query->andFilterWhere(['like', 'cpf', trim($this->cpf)])
            ->andFilterWhere(['like', 'Condutor.nit', trim($this->nit)])
            ->andFilterWhere(['like', 'rg', trim($this->rg)])
            ->andFilterWhere(['like', 'cep', trim($this->cep)])
            ->andFilterWhere(['like', 'Condutor.nome', trim($this->nome)])
            ->andFilterWhere(['like', 'endereco', trim($this->endereco)])
            ->andFilterWhere(['like', 'bairro', trim($this->bairro)])
            ->andFilterWhere(['like', 'telefone', trim($this->telefone)])
            ->andFilterWhere(['like', 'email', trim($this->email)]);

        return $dataProvider;
    }
}
