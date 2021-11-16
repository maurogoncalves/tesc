<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "PontoEscola".
 *
 * @property string $id ID
 * @property string $idPonto Ponto
 * @property string $idEscola Escola
 *
 * @property Escola $escola
 * @property Ponto $ponto
 */
class PontoEscola extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PontoEscola';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idPonto', 'idEscola'], 'required'],
            [['idPonto', 'idEscola','sentido'], 'integer'],
            
            [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
            [['idPonto'], 'exist', 'skipOnError' => true, 'targetClass' => Ponto::className(), 'targetAttribute' => ['idPonto' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idPonto' => 'Ponto',
            'idEscola' => 'Escola',
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
    public function getPonto()
    {
        return $this->hasOne(Ponto::className(), ['id' => 'idPonto']);
    }
}
