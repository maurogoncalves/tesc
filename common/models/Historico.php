<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Historico".
 *
 * @property string $id Cód.
 * @property string $idCondutorRota Condutor
 * @property string $idCondutor Condutor
 * @property string $data Data
 * @property string $checkIn Check In
 * @property string $checkOut Check Out
 *
 * @property CondutorRota $condutorRota
 * @property HistoricoAluno[] $historicoAlunos
 * @property HistoricoEscola[] $historicoEscolas
 */
class Historico extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Historico';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idCondutorRota', 'data', 'checkIn', 'checkOut'], 'required'],
            [['idCondutorRota','idVeiculo'], 'integer'],
            [['data', 'checkIn', 'checkOut', 'idCondutor','distanciaTotal'], 'safe'],
            [['idCondutorRota'], 'exist', 'skipOnError' => true, 'targetClass' => CondutorRota::className(), 'targetAttribute' => ['idCondutorRota' => 'id']],
            [['idVeiculo'], 'exist', 'skipOnError' => true, 'targetClass' => Veiculo::className(), 'targetAttribute' => ['idVeiculo' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Cód.',
            'idVeiculo' => 'Veículo',
            'idCondutorRota' => 'Rota',
            'idCondutor' => 'Condutor',
            'data' => 'Data',
            'checkIn' => 'Check In',
            'checkOut' => 'Check Out',
        ];
    }

    


    public function fields()
    {
        $fields = parent::fields();

        $fields['alunos'] = 'historicoAlunos';   
        $fields['escolas'] = 'historicoEscolas';
        //$fields['condutor'] =   'condutor';  
        
        return $fields;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutorRota()
    {
        return $this->hasOne(CondutorRota::className(), ['id' => 'idCondutorRota']);
    }

    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }


    public function getVeiculo()
    {
        return $this->hasOne(Veiculo::className(), ['id' => 'idVeiculo']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistoricoAlunos()
    {
        return $this->hasMany(HistoricoAluno::className(), ['idHistorico' => 'id']);
    }

 
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistoricoEscolas()
    {
        return $this->hasMany(HistoricoEscola::className(), ['idHistorico' => 'id']);
    }

    public function getTimeline(){
        return HistoricoVeiculo::find()
            ->andWhere(['idVeiculo' => $this->idVeiculo])
            ->andWhere(['idCondutor' => $this->idCondutor])
            ->andWhere(['>=','data' , $this->data.' '.$this->checkIn])
            ->andWhere(['<=','data' , $this->data.' '.$this->checkOut])
            ->all();
    }

    public function getOcorrencias(){
        return Ocorrencia::find()
            ->andWhere(['idVeiculo' => $this->idVeiculo])
            ->andWhere(['idCondutor' => $this->idCondutor])
            ->andWhere(['>=','data' , $this->data.' '.$this->checkIn])
            ->andWhere(['<=','data' , $this->data.' '.$this->checkOut])
            ->all();
    }
    public function getComunicados(){
        return Comunicado::find()
            ->andWhere(['idCondutor' => $this->idCondutor])
            ->andWhere(['>=','data' , $this->data.' '.$this->checkIn])
            ->andWhere(['<=','data' , $this->data.' '.$this->checkOut])
            ->all();
    }

}
