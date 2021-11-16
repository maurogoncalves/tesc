<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider; 
use common\models\SolicitacaoTransporte;

/**
 * SolicitacaoTransporteSearch represents the model behind the search form about `common\models\SolicitacaoTransporte`.
 */
class SolicitacaoTransporteSearch extends SolicitacaoTransporte
{
    public $condutorAlvara;
    public $filtrarCondutorIda = false;
    public $filtrarCondutorVolta = false;

    public $condutorIdaNome;
    public $condutorIdaAlvara;
    public $condutorIdaTelefone;

    public $condutorVoltaNome;
    public $condutorVoltaAlvara;
    public $condutorVoltaTelefone;

    public $necessidadeEspecial;

    public $grupo;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idAluno', 'idEscola', 'status', 'modalidadeBeneficio', 'barreiraFisica','condutorIdaNome'], 'integer'],
            [['grupo','novaSolicitacao','data', 'justificativaBarreiraFisica', 'cartaoPasseEscolar', 'cartaoValeTransporte','tipoSolicitacao','condutorIdaNome','condutorIdaAlvara','condutorIdaTelefone','condutorVoltaNome','condutorVoltaAlvara','condutorVoltaTelefone','anoVigente','tipoFrete','ultimaMovimentacao'], 'safe'],
            [['distanciaEscola','necessidadeEspecial'], 'number'],
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
    public function search($params, $listOfIds=[])
    {
        $query = SolicitacaoTransporte::find();
        //if(Usuario::permissoes([Usuario::PERFIL_DRE,Usuario::PERFIL_DIRETOR,Usuario::PERFIL_SECRETARIO ]))
            $query->joinWith('escola');
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
        if($this->grupo){
            $novaLista = [];
            $solicitacoes = SolicitacaoTransporte::find()->where(['in', 'SolicitacaoTransporte.id', $listOfIds ])->all();
            foreach($solicitacoes as $solicitacao) {
                if(UsuarioGrupo::solicitacaoPertenceGrupo($solicitacao, $this->grupo))
                 $novaLista[] = $solicitacao->id;
            }
            // print_r($novaLista);
            // die(1);
            $listOfIds = $novaLista;
        }
        $listOfIds[] = 0;
        if($this->data){
            $data = explode('- ', $this->data);
        
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

            $data[0] = explode('/', $data[0]);
            $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
            
            // print_r($data[0]);
            // print_r($data[1]);
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(SolicitacaoTransporte.data,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(SolicitacaoTransporte.data,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );

        }
        if($this->ultimaMovimentacao){
            $data = explode('- ', $this->ultimaMovimentacao);
        
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

            $data[0] = explode('/', $data[0]);
            $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
            
            // print_r($data[0]);
            // print_r($data[1]);
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(SolicitacaoTransporte.ultimaMovimentacao,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(SolicitacaoTransporte.ultimaMovimentacao,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );

        }

        if($listOfIds)
        $query->andFilterWhere(['in', 'SolicitacaoTransporte.id', $listOfIds ]);
        
        $query->joinWith(['condutor','aluno']);
        // 
        // condutorIdaAlvara
        // condutorIdaTelefone
        $dataProvider->sort->attributes['idAluno'] = [
            'asc' => ['Aluno.nome' => SORT_ASC],
            'desc' => ['Aluno.nome' => SORT_DESC],
        ];
        // $dataProvider->sort->attributes['condutorIdaNome'] = [
        //     'asc' => ['Condutor.alvara' => SORT_ASC],
        //     'desc' => ['Condutor.alvara' => SORT_DESC],
        // ];
        
        // $dataProvider->sort->attributes['condutorTelefone'] = [
        //     'asc' => ['Condutor.telefone' => SORT_ASC],
        //     'desc' => ['Condutor.telefone' => SORT_DESC],
        // ];

        if($this->condutorIdaNome){
            $this->filtrarCondutorIda = $this->condutorIdaNome;
        }

        if($this->condutorIdaAlvara){
            $this->filtrarCondutorIda = $this->condutorIdaAlvara;
        }
        
        if($this->condutorIdaTelefone){
            $this->filtrarCondutorIda = $this->condutorIdaTelefone;
        }

        if($this->condutorVoltaNome){
            $this->filtrarCondutorVolta = $this->condutorVoltaNome;
        }

        if($this->condutorVoltaAlvara){
            $this->filtrarCondutorVolta = $this->condutorVoltaAlvara;
        }
        
        if($this->condutorVoltaTelefone){
            $this->filtrarCondutorVolta = $this->condutorVoltaTelefone;
        }

        if($this->necessidadeEspecial){
            $necessidades = AlunoNecessidadesEspeciais::find()->groupBy('idAluno')->all();
            $listOfIds = [];
            $operator = 'in';
            foreach($necessidades as $necessidade) {
                array_push($listOfIds, $necessidade->idAluno);
            } 

            // necessidadeEspecial = NÃO = 1
            if($this->necessidadeEspecial == 1)
                $operator = 'not in';
            $query->andFilterWhere([$operator, 'SolicitacaoTransporte.idAluno', $listOfIds ]);
        }
        //condutorIdaTelefone
        // 
        if($this->filtrarCondutorIda){
            $rotas = CondutorRota::find()->select('id')->where(['idCondutor' => $this->filtrarCondutorIda])->all();
            $rotas = array_column($rotas,'id');
            $query->andFilterWhere(['in', 'SolicitacaoTransporte.idRotaIda', $rotas ]);
        }

        if($this->filtrarCondutorVolta){
            $rotas = CondutorRota::find()->select('id')->where(['idCondutor' => $this->filtrarCondutorVolta])->all();
            $rotas = array_column($rotas,'id');
            $query->andFilterWhere(['in', 'SolicitacaoTransporte.idRotaVolta', $rotas ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'SolicitacaoTransporte.id' => $this->id,
            'SolicitacaoTransporte.idAluno' => $this->idAluno,
            'SolicitacaoTransporte.idEscola' => $this->idEscola,
            'SolicitacaoTransporte.tipoFrete' => $this->tipoFrete, 
            // 'SolicitacaoTransporte.data' => $this->data,
            'SolicitacaoTransporte.status' => $this->status,
            'SolicitacaoTransporte.novaSolicitacao' => $this->novaSolicitacao,
            'SolicitacaoTransporte.modalidadeBeneficio' => $this->modalidadeBeneficio,
            'SolicitacaoTransporte.barreiraFisica' => $this->barreiraFisica,
            'SolicitacaoTransporte.distanciaEscola' => $this->distanciaEscola,
            'SolicitacaoTransporte.tipoSolicitacao' => $this->tipoSolicitacao,
            'SolicitacaoTransporte.anoVigente' => $this->anoVigente,
            // 'SolicitacaoTransporte.idCondutorIda' => $this->idCondutorIda,
            // 'SolicitacaoTransporte.idCondutorVolta' => $this->idCondutorVolta,
            // 'Condutor.alvara'  => $this->condutorAlvara,
            // 'Condutor.telefone'  => $this->condutorTelefone,
        ]);

        $query->andFilterWhere(['like', 'SolicitacaoTransporte.justificativaBarreiraFisica', $this->justificativaBarreiraFisica])
            ->andFilterWhere(['like', 'SolicitacaoTransporte.cartaoPasseEscolar', $this->cartaoPasseEscolar])
            ->andFilterWhere(['like', 'SolicitacaoTransporte.cartaoValeTransporte', $this->cartaoValeTransporte]);
        return $dataProvider;
    }
}