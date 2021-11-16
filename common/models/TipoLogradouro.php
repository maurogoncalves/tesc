<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "TB_TIPO_LOGRADOURO".
 *
 * @property int $ID_TIPO_LOGRADOURO
 * @property string $TIPO_ABREVIADO
 * @property string $TIPO
 * @property int $STATUS
 * @property string $DT_ATUALIZACAO
 *
 * @property TBCEP[] $tBCEPs
 */
class TipoLogradouro extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'TB_TIPO_LOGRADOURO';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('ipplanDb');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['TIPO_ABREVIADO', 'TIPO'], 'string'],
            [['TIPO', 'DT_ATUALIZACAO'], 'required'],
            [['STATUS'], 'integer'],
            [['DT_ATUALIZACAO'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID_TIPO_LOGRADOURO' => 'I D T I P O L O G R A D O U R O',
            'TIPO_ABREVIADO' => 'T I P O A B R E V I A D O',
            'TIPO' => 'T I P O',
            'STATUS' => 'S T A T U S',
            'DT_ATUALIZACAO' => 'D T A T U A L I Z A C A O',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTBCEPs()
    {
        return $this->hasMany(TBCEP::className(), ['ID_TIPO_LOGRADOURO' => 'ID_TIPO_LOGRADOURO']);
    }
}
