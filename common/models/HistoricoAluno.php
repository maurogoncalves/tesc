<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "HistoricoAluno".
 *
 * @property string $id Cód.
 * @property string $idHistorico Histórico
 * @property string $idAluno Aluno
 * @property string $checkIn Check In
 * @property string $checkOut Check Out
 *
 * @property Aluno $aluno
 * @property Historico $historico
 */
class HistoricoAluno extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'HistoricoAluno';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idHistorico', 'idAluno', 'checkIn', 'checkOut'], 'required'],
            [['idHistorico', 'idAluno'], 'integer'],
            [['checkIn', 'checkOut','lat','lng'], 'safe'],
            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],
            [['idHistorico'], 'exist', 'skipOnError' => true, 'targetClass' => Historico::className(), 'targetAttribute' => ['idHistorico' => 'id']],
        ];
    }
    
    public function fields()
    {
        $fields = parent::fields();
        $fields['data'] = 'data';   
        $fields['alunoNome'] = 'alunoNome';
    
        // $fields['aluno'] = 'aluno';   
        return $fields;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Cód.',
            'idHistorico' => 'Histórico',
            'idAluno' => 'Aluno',
            'checkIn' => 'Check In',
            'checkOut' => 'Check Out',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getData()
    {
        return $this->historico->data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluno()
    {
        return $this->hasOne(Aluno::className(), ['id' => 'idAluno']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistorico()
    {
        return $this->hasOne(Historico::className(), ['id' => 'idHistorico']);
    }
        public function getHistoricoRelatorio()
    {
        return $this->hasOne(Historico::className(), ['idHistorico' => 'id']);
    }

    public function getEscola(){
        return $this->hasOne(Escola::className(), ['id' => 'idEscola'])->via('aluno');
    }


    public function getAlunoNome(){
        return $this->aluno->nome;
    }
}
