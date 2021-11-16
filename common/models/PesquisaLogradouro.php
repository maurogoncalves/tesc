<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "VW_PESQUISA_LOGRADOURO".
 *
 * @property int $ID_CEP
 * @property int $ID_LOGRADOURO
 * @property string $LOGRADOURO
 * @property string $TIPO_LOGRADOURO
 * @property string $BAIRRO
 * @property string $CEP
 */
class PesquisaLogradouro extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'VW_PESQUISA_LOGRADOURO';
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
            [['ID_CEP', 'ID_LOGRADOURO'], 'required'],
            [['ID_CEP', 'ID_LOGRADOURO'], 'integer'],
            [['LOGRADOURO', 'TIPO_LOGRADOURO', 'BAIRRO', 'CEP'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID_CEP' => 'I D C E P',
            'ID_LOGRADOURO' => 'I D L O G R A D O U R O',
            'LOGRADOURO' => 'L O G R A D O U R O',
            'TIPO_LOGRADOURO' => 'T I P O L O G R A D O U R O',
            'BAIRRO' => 'B A I R R O',
            'CEP' => 'C E P',
        ];
    }
}
