<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SolicitacaoCredito;
use common\models\Usuario;
use common\models\EscolaDiretor;
use common\models\EscolaSecretario;

/**
 * SolicitacaoCreditoSearch represents the model behind the search form about `common\models\SolicitacaoCredito`.
 */

class SolicitacaoCreditoSearch extends SolicitacaoCredito
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idEscola','status','mesInicio','mesFim','tipoSolicitacao','anoSol'], 'integer'],
            [['inicio', 'fim', 'criado'], 'safe'],
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
        $query = SolicitacaoCredito::find();

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
        $query->joinWith('escola');

        if(Usuario::permissao(Usuario::PERFIL_DIRETOR) ){
          $ids = EscolaDiretor::listaEscolas();
          $ids[] = 9999999;
          $query->andFilterWhere(['in','idEscola', $ids]);
        }
        
        if(Usuario::permissao(Usuario::PERFIL_SECRETARIO) ){ 
          $ids = EscolaSecretario::listaEscolas();
          $ids[] = 9999999;
          $query->andFilterWhere(['in','idEscola', $ids]);
        }  
        if(Usuario::permissao(Usuario::PERFIL_DRE) )
            $dataProvider->query->andFilterWhere(['Escola.unidade' => Escola::UNIDADE_ESTADUAL]);
         if(!empty($this->inicio)) {
            // $date = \DateTime::createFromFormat( 'd/m/Y', $this->inicio);
            // $query->andFilterWhere(['>=', 'inicio', $date->format('Y-m-d')]);

        }
        if($this->inicio){
            $data = explode('- ', $this->inicio);
        
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

            $data[0] = explode('/', $data[0]);
            $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(inicio,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(inicio,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );
        }
        if($this->fim){
            $data = explode('- ', $this->fim);
       
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

            $data[0] = explode('/', $data[0]);
            $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(fim,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(fim,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );
        }
		
		 if($this->anoSol){
            $query->andFilterWhere ( [ '=' , 'anoSol' , $this->ano] );
        }
		
        // if(!empty($this->fim)) {
        //     $date = \DateTime::createFromFormat( 'd/m/Y', $this->fim); 
        //     $query->andFilterWhere(['<=', 'fim', $date->format('Y-m-d')]);
        // }
        // grid filtering conditions
        $query->andFilterWhere([
            'SolicitacaoCredito.tipoSolicitacao' => $this->tipoSolicitacao,
            'SolicitacaoCredito.id' => $this->id,
            'SolicitacaoCredito.idEscola' => $this->idEscola,
            'mesInicio' => $this->mesInicio,
            'mesFim' => $this->mesFim,
            'SolicitacaoCredito.criado' => $this->criado,
            'SolicitacaoCredito.status' => $this->status,
			'anoSol' => $this->anoSol,
			
        ]);
		
        return $dataProvider;
    }
}
