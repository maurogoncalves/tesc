<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Atendimento".
 *
 * @property string $id Código
 * @property string $nome Nome
 */
class Atendimento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Atendimento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome'], 'string', 'max' => 50],
             [['nome'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'nome' => 'Nome',
        ];
    }
}
