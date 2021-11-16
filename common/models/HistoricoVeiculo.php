<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "HistoricoVeiculo".
 *
 * @property int $id Código
 * @property string $data Registrado
 * @property string $idVeiculo Veículo
 * @property string $idCondutor Condutor
 * @property int $lat Lat
 * @property int $lng Lng
 *
 * @property Condutor $condutor
 * @property Veiculo $veiculo
 */
class HistoricoVeiculo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'HistoricoVeiculo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data', 'idVeiculo', 'idCondutor', 'lat', 'lng'], 'required'],
            [['data','endereco'], 'safe'],
            [['idVeiculo', 'idCondutor'], 'integer'],
            [['idCondutor'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idCondutor' => 'id']],
            [['idVeiculo'], 'exist', 'skipOnError' => true, 'targetClass' => Veiculo::className(), 'targetAttribute' => ['idVeiculo' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'data' => 'Registrado',
            'idVeiculo' => 'Veículo',
            'idCondutor' => 'Condutor',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'endereco' => 'Endereço',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVeiculo()
    {
        return $this->hasOne(Veiculo::className(), ['id' => 'idVeiculo']);
    }
}
