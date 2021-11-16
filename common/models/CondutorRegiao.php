<?php

namespace common\models;

use Yii;

class CondutorRegiao extends \yii\db\ActiveRecord
{

    
    public static function tableName()
    {
        return 'CondutorRegiao';
    }
    public function rules()
    {
        return [
            [['idCondutor','regiao'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idCondutor' => 'Condutor',
            'regiao' => 'Região'
        ];
    }

    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }

    public function getRegiao()
    {
        return Condutor::ARRAY_REGIAO[$this->regiao];
    }
}