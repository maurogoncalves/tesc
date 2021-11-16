<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "EscolaAtendimentoHomologacao".
 *
 * @property string $id ID
 * @property string $idEscola Escola
 * @property string $idAtendimento Atendimento
 *
 * @property Escola $escola
 */
class EscolaAtendimentoHomologacao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'EscolaAtendimentoHomologacao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idEscola', 'idAtendimento'], 'integer'],
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
    public function getEscola()
    {
        return $this->hasOne(Escola::className(), ['id' => 'idEscola']);
    }
}
