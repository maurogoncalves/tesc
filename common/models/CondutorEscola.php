<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "CondutorEscola".
 *
 * @property string $id ID
 * @property string $idCondutor Condutor
 * @property string $idEscola Escola
 *
 * @property Condutor $condutor
 * @property Escola $escola
 */
class CondutorEscola extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'CondutorEscola';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idCondutor', 'idEscola'], 'required'],
            [['idCondutor', 'idEscola'], 'integer'],
            [['idCondutor'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idCondutor' => 'id']],
            [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

       
        $fields['escola'] = 'escola';  
        //$fields['condutor'] =   'condutor';  
        
        return $fields;
    }
    // public function extraFields(){
    //    return ['condutor'];
    // }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idCondutor' => 'Condutor',
            'idEscola' => 'Escola',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscola()
    {
        return $this->hasOne(Escola::className(), ['id' => 'idEscola']);
    }
}
