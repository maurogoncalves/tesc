<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "CalendarioEscola".
 *
 * @property int $id Código
 * @property int $idCalendario Calendário
 * @property int $tipoEscola Tipo da escola
 *
 * @property Calendario $calendario
 */
class CalendarioEscola extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'CalendarioEscola';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idCalendario', 'tipoEscola'], 'required'],
            [['idCalendario', 'tipoEscola'], 'integer'],
            [['idCalendario'], 'exist', 'skipOnError' => true, 'targetClass' => Calendario::className(), 'targetAttribute' => ['idCalendario' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idCalendario' => 'Calendário',
            'tipoEscola' => 'Tipo da escola',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalendario()
    {
        return $this->hasOne(Calendario::className(), ['id' => 'idCalendario']);
    }
}
