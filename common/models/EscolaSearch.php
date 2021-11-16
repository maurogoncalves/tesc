<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Escola;
use common\models\EscolaDiretor;
use common\models\EscolaSecretario;
use common\models\Usuario;
/**
 * EscolaSearch represents the model behind the search form about `common\models\Escola`.
 */
class EscolaSearch extends Escola
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tipo','unidade'], 'integer'],
            [['nome', 'endereco', 'telefone', 'email', 'codigoCie', 'regiao', 'ensino'], 'safe'],
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
        $query = Escola::find();

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
        
        if(Usuario::permissao(Usuario::PERFIL_DRE) )
            $dataProvider->query->andFilterWhere(['Escola.unidade' => Escola::UNIDADE_ESTADUAL]); 

        
        if ($this->ensino)
        {
            $query->joinWith('atendimento');
            $query->andFilterWhere(['=', 'idAtendimento', $this->ensino]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'tipo' => $this->tipo,
            'unidade' => $this->unidade,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'regiao' => $this->regiao
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'endereco', $this->endereco])
            ->andFilterWhere(['like', 'telefone', $this->telefone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'codigoCie', $this->codigoCie]);

        return $dataProvider;
    }
}
