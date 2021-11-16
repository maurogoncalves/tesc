<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\NotificacaoEnviada;

/**
 * NotificacaoEnviadaSearch represents the model behind the search form about `common\models\NotificacaoEnviada`.
 */
class NotificacaoEnviadaSearch extends NotificacaoEnviada
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idUsuario', 'tipo','idAluno'], 'integer'],
            [['data', 'idFirebase', 'texto'], 'safe'],
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
        $query = NotificacaoEnviada::find();

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

        if($this->data) {
            $data = explode('- ', $this->data);
           
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

            $data[0] = explode('/', $data[0]);
            $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(data,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(data,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'idUsuario' => $this->idUsuario,
            'idAluno' => $this->idAluno,
            
            //'data' => $this->data,
            'tipo' => $this->tipo,
        ]);

        $query->andFilterWhere(['like', 'texto', $this->texto]);

        return $dataProvider;
    }
}
