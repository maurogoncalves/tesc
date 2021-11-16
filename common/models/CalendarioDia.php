<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "CalendarioDia".
 *
 * @property int $id Código
 * @property int $idCalendario Calendário
 * @property string $data Data
 * @property int $tipo Tipo
 *
 * @property Calendario $calendario
 */
class CalendarioDia extends \yii\db\ActiveRecord
{
    const TIPO_COM_AULA = 1;
    const TIPO_SEM_AULA = 2;

    const ARRAY_TIPO = [
         self::TIPO_COM_AULA => 'Dia letivo',
         self::TIPO_SEM_AULA => 'Dia não letivo',
     ];
 
    /**
     * {@inheritdoc}
     */ 
    public static function tableName()
    {
        return 'CalendarioDia';
    }

    /**
     * {@inheritdoc} 
     */
    public function rules()
    {
        return [
            [['idCalendario', 'data', 'tipo','descricao'], 'required'],
            [['idCalendario', 'tipo'], 'integer'],
            // [['data'],'unique'],
            [['data','descricao'], 'safe'],
            [['descricao'], 'string', 'max' => 50],
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
            'data' => 'Data',
            'tipo' => 'Tipo',
            'descricao' => 'Descrição',
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
