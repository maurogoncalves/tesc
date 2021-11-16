<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "AlunoCurso".
 *
 * @property int $id ID
 * @property int $tipo Tipo
 * @property string $idAluno Aluno
 * @property int $dia Dia da semana
 *
 * @property Aluno $aluno
 */
class AlunoCurso extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'AlunoCurso';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idAluno', 'dia'], 'required'],
            [['tipo', 'idAluno', 'dia'], 'integer'],
            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo',
            'idAluno' => 'Aluno',
            'dia' => 'Dia da semana',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluno()
    {
        return $this->hasOne(Aluno::className(), ['id' => 'idAluno']);
    }
}
