<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Ocorrencia;
use kartik\daterange\DateRangeBehavior;

/**
 * OcorrenciaSearch represents the model behind the search form about `common\models\Ocorrencia`.
 */
class OcorrenciaSearch extends Ocorrencia
{
    public $dataInicial;
    public $dataFinal;

    // public function behaviors()
    // {
    //     return [
    //         [
    //             'class' => DateRangeBehavior::className(),
    //             'attribute' => 'data',
    //             'dateStartAttribute' => 'dataInicial',
    //             'dateEndAttribute' => 'dataFinal',
    //         ]
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idCondutor', 'idCondutorRota', 'idJustificativa', 'idVeiculo'], 'integer'],
            [['data', 'descricao','dataInicial','dataFinal'], 'safe'],
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
        $query = Ocorrencia::find();

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
        if($this->data){
            $data = explode('- ', $this->data);
            //go horse passando pelo seu code editor 
            /*
                       .''
              ._.-.___.' (`\
             //(        ( `'
            '/ )\ ).__. ) 
            ' <' `\ ._/'\
               `   \     \
            */
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(data,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(data,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );

        }
    

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'data' => $this->data,
            'idCondutor' => $this->idCondutor,
            'idCondutorRota' => $this->idCondutorRota,
            'idJustificativa' => $this->idJustificativa,
            'idVeiculo' => $this->idVeiculo,
        ]);

        $query->andFilterWhere(['like', 'descricao', $this->descricao]);

        return $dataProvider;
    }
}
