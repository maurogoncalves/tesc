<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "AlunoRota".
 *
 * @property string $id ID
 * @property string $idCondutorRota Rota
 * @property string $idAluno Aluno
 * @property string $idEscola Escola
 *
 * @property Aluno $aluno
 * @property CondutorRota $condutorRota
 * @property Escola $escola
 */
class AlunoRota extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'AlunoRota';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idCondutorRota', 'idAluno', 'idEscola'], 'required'],
            [['idCondutorRota', 'idAluno', 'idEscola'], 'integer'],
            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],
            [['idCondutorRota'], 'exist', 'skipOnError' => true, 'targetClass' => CondutorRota::className(), 'targetAttribute' => ['idCondutorRota' => 'id']],
            [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
        ];
    }
public function fields()
    {
        $fields = parent::fields();

        $fields['aluno'] = 'aluno';   
    
        //$fields['condutor'] =   'condutor';  
        
        return $fields;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idCondutorRota' => 'Rota',
            'idAluno' => 'Aluno',
            'idEscola' => 'Escola',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluno()
    {
        return $this->hasOne(Aluno::className(), ['id' => 'idAluno']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutorRota()
    {
        return $this->hasOne(CondutorRota::className(), ['id' => 'idCondutorRota']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscola()
    {
        return $this->hasOne(Escola::className(), ['id' => 'idEscola']);
    }
}
