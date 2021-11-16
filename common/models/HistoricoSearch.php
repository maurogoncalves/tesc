<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Historico;

/**
 * HistoricoSearch represents the model behind the search form about `common\models\Historico`.
 */
class HistoricoSearch extends Historico
{
    public $dataInicial;
    public $dataFinal;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idCondutorRota', 'idCondutor', 'idVeiculo'], 'integer'],
            [['data', 'checkIn', 'checkOut','dataInicial','dataFinal'], 'safe'],
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
        $query = Historico::find();

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
        // if($this->checkIn){
        //      $query->andFilterWhere(['=','TIME_FORMAT(checkIn,"%h:%i")', $this->checkIn]);
        // }
        // if($this->checkOut && $this->checkOut != '00:00'){
        //      $query->andFilterWhere(['<=','checkOut', $this->checkOut]);
        // }
    
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
            'idCondutorRota' => $this->idCondutorRota,
            'idCondutor' => $this->idCondutor,
            'idVeiculo' => $this->idVeiculo,
            //'data' => $this->data,
            'TIME_FORMAT(checkIn,"%H:%i")' => $this->checkIn,
            'TIME_FORMAT(checkOut,"%H:%i")' => $this->checkOut,
        ]);

        $query->orderBy(['data'=>SORT_DESC]);

        return $dataProvider;
    }
}
