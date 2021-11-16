<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "HistoricoEscola".
 *
 * @property string $id Cód.
 * @property string $idHistorico Histórico
 * @property string $idEscola Escola
 * @property string $checkOut Check Out
 *
 * @property Escola $escola
 * @property Historico $historico
 */
class HistoricoEscola extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'HistoricoEscola';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idHistorico', 'idEscola', 'checkOut'], 'required'],
            [['idHistorico', 'idEscola'], 'integer'],
            [['checkOut','lat','lng'], 'safe'],
            [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
            [['idHistorico'], 'exist', 'skipOnError' => true, 'targetClass' => Historico::className(), 'targetAttribute' => ['idHistorico' => 'id']],
        ];
    }
    
    public function fields()
    {
        $fields = parent::fields();
        $fields['escola'] = 'escola';   
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
            'idEscola' => 'Escola',
            'checkOut' => 'Check Out',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscola()
    {
        return $this->hasOne(Escola::className(), ['id' => 'idEscola']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistorico()
    {
        return $this->hasOne(Historico::className(), ['id' => 'idHistorico']);
    }
}
