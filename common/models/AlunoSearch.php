<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Aluno;

/**
 * AlunoSearch represents the model behind the search form about `common\models\Aluno`.
 */
class AlunoSearch extends Aluno
{
    public $necessidadeEspecial;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idEscola', 'modalidadeBeneficio', 'barreiraFisica', 'idRgAluno', 'idComprovanteEndereco', 'idRgResponsavel', 'idDeclaracaoVizinhos', 'idLaudoMedico', 'idTransporteEspecialAdaptado', 'idDeclaracaoInexistenciaVaga','tipoFrete'], 'integer'],
            [['nome', 'dataNascimento', 'nomeMae', 'nomePai', 'RA', 'endereco', 'cartaoPasseEscolar', 'horarioEntrada', 'horarioSaida', 'telefoneResidencial', 'telefoneResidencial2', 'telefoneCelular', 'telefoneCelular2','cartaoPasseEscolar','status','redeEnsino','modalidadeBeneficio','tipoFrete','turma','turno','serie'], 'safe'],
            [['lat', 'lng', 'distanceEscola','ensino','necessidadeEspecial'], 'number'],
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
        $query = Aluno::find();

        // add conditions that should always apply here
        $query->joinWith(['solicitacao','escola']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->attributes['ensino'] = [
            'asc' => ['Aluno.ensino' => SORT_ASC],
            'desc' => ['Aluno.ensino' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['status'] = [
            'asc' => ['SolicitacaoTransporte.status' => SORT_ASC],
            'desc' => ['SolicitacaoTransporte.status' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['modalidadeBeneficio'] = [
            'asc' => ['SolicitacaoTransporte.modalidadeBeneficio' => SORT_ASC],
            'desc' => ['SolicitacaoTransporte.modalidadeBeneficio' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['tipoFrete'] = [
            'asc' => ['SolicitacaoTransporte.tipoFrete' => SORT_ASC],
            'desc' => ['SolicitacaoTransporte.tipoFrete' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['redeEnsino'] = [
            'asc' => ['Escola.unidade' => SORT_ASC],
            'desc' => ['Escola.unidade' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if(Usuario::permissao(Usuario::PERFIL_DIRETOR) ){
          $ids = EscolaDiretor::listaEscolas();
          $ids[] = 9999999;
          $query->andFilterWhere(['in','Escola.id', $ids]);
        }
        
        if(Usuario::permissao(Usuario::PERFIL_SECRETARIO) ){
          $ids = EscolaSecretario::listaEscolas();
          $ids[] = 9999999;
          $query->andFilterWhere(['in','Escola.id', $ids]);
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
            $query->andFilterWhere([$operator, 'Aluno.id', $listOfIds ]);
        }

        if(Usuario::permissao(Usuario::PERFIL_DRE) )
            $query->andFilterWhere(['Escola.unidade' => Escola::UNIDADE_ESTADUAL]);
        // grid filtering conditions
        $query->andFilterWhere([
            'Aluno.id' => $this->id,
            'Aluno.idEscola' => $this->idEscola,
            'Aluno.dataNascimento' => $this->dataNascimento,
            'Aluno.lat' => $this->lat,
            'Aluno.lng' => $this->lng,
            'Aluno.ensino' => $this->ensino,
            'Aluno.serie' => $this->serie,
            'Aluno.turma' => $this->turma,
			'Aluno.turno' => $this->turno,
            // 'modalidadeBeneficio' => $this->modalidadeBeneficio,
            'Aluno.horarioEntrada' => $this->horarioEntrada,
            'Aluno.horarioSaida' => $this->horarioSaida,
            'Aluno.distanceEscola' => $this->distanceEscola,
            'Aluno.barreiraFisica' => $this->barreiraFisica,
            'Aluno.idRgAluno' => $this->idRgAluno,
            'Aluno.idComprovanteEndereco' => $this->idComprovanteEndereco,
            'Aluno.idRgResponsavel' => $this->idRgResponsavel,
            'Aluno.idDeclaracaoVizinhos' => $this->idDeclaracaoVizinhos,
            'Aluno.idLaudoMedico' => $this->idLaudoMedico,
            'Aluno.idTransporteEspecialAdaptado' => $this->idTransporteEspecialAdaptado,
            'Aluno.idDeclaracaoInexistenciaVaga' => $this->idDeclaracaoInexistenciaVaga,
        ]);

        $query->andFilterWhere(['like', 'Aluno.nome', $this->nome])
            ->andFilterWhere(['like', 'Aluno.nomeMae', $this->nomeMae])
            ->andFilterWhere(['like', 'Aluno.nomePai', $this->nomePai])
            ->andFilterWhere(['like', 'Aluno.RA', $this->RA])
            ->andFilterWhere(['like', 'Aluno.endereco', $this->endereco])
            ->andFilterWhere(['like', 'Aluno.cartaoPasseEscolar', $this->cartaoPasseEscolar])
            ->andFilterWhere(['like', 'Aluno.cartaoValeTransporte', $this->cartaoValeTransporte])
            ->andFilterWhere(['like', 'Aluno.telefoneResidencial', $this->telefoneResidencial])
            ->andFilterWhere(['like', 'Aluno.telefoneResidencial2', $this->telefoneResidencial2])
            ->andFilterWhere(['like', 'Aluno.telefoneCelular', $this->telefoneCelular])
            ->andFilterWhere(['like', 'Aluno.telefoneCelular2', $this->telefoneCelular2]);

        // Aqui vamos ter que fazer na mão
        $models = [];
        foreach($query->all() as $model)
        {
            $filtroOK = true;

            if ($this->status && $model->solicitacao->status != $this->status)
                $filtroOK = false;
            
            if ($this->modalidadeBeneficio && $model->solicitacao->modalidadeBeneficio != $this->modalidadeBeneficio)
                $filtroOK = false;

            // if ($this->redeEnsino)
            // {
            //     $filtroOK = false;
            //     foreach($model->escola->atendimento as $rede)
            //     {
            //         if ($rede->idAtendimento == $this->redeEnsino)
            //             $filtroOK = true;
            //     }
            // }

            if ($this->tipoFrete && $model->solicitacao->tipoFrete != $this->tipoFrete)
                $filtroOK = false;

            if ($filtroOK)
                $models[] = $model;
        }

        // return $dataProvider;
        return $models;
    }
}
