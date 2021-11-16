<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "EscolaAtendimento".
 *
 * @property string $id ID
 * @property string $idEscola Escola
 * @property string $idAtendimento Atendimento
 *
 * @property Atendimento $atendimento
 * @property Escola $escola
 */
class EscolaAtendimento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'EscolaAtendimento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idEscola', 'idAtendimento'], 'integer'],
            // [['idAtendimento'], 'exist', 'skipOnError' => true, 'targetClass' => Atendimento::className(), 'targetAttribute' => ['idAtendimento' => 'id']],
            [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idEscola' => 'Escola',
            'idAtendimento' => 'Atendimento',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAtendimento()
    {
        return $this->hasOne(Atendimento::className(), ['id' => 'idAtendimento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscola()
    {
        return $this->hasOne(Escola::className(), ['id' => 'idEscola']);
    }
}
