<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Poc".
 *
 * @property int $id
 * @property string $arquivo
 * @property string $data
 * @property string $texto
 */
class Poc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Poc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['arquivo', 'data', 'texto'], 'required'],
            [['data'], 'safe'],
            [['texto'], 'string'],
            [['arquivo'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'arquivo' => 'Arquivo',
            'data' => 'Data',
            'texto' => 'Texto',
        ];
    }
}
