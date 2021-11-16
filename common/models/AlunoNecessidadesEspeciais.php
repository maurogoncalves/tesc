<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "AlunoNecessidadesEspeciais".
 *
 * @property string $id Código
 * @property string $idAluno Aluno
 * @property string $idNecessidadesEspeciais Necessidade Especial
 *
 * @property Aluno $aluno
 * @property Necessidadesespeciais $necessidadesEspeciais
 */
class AlunoNecessidadesEspeciais extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'AlunoNecessidadesEspeciais';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idAluno', 'idNecessidadesEspeciais'], 'required'],
            [['idAluno', 'idNecessidadesEspeciais'], 'integer'],
            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],
            [['idNecessidadesEspeciais'], 'exist', 'skipOnError' => true, 'targetClass' => NecessidadesEspeciais::className(), 'targetAttribute' => ['idNecessidadesEspeciais' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idAluno' => 'Aluno',
            'idNecessidadesEspeciais' => 'Necessidade Especial',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluno()
    {
        return $this->hasOne(Aluno::className(), ['id' => 'idAluno']);
    }

    public function getSolicitacoes()
    {
        return $this->hasMany(SolicitacaoTransporte::className(), ['idAluno' => 'idAluno']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNecessidadesEspeciais()
    {
        return $this->hasOne(NecessidadesEspeciais::className(), ['id' => 'idNecessidadesEspeciais']);
    }

    
      public function getEscolas()
    {
        return $this->hasMany(Escola::className(), ['id' => 'idEscola'])->via('aluno');
    }
}
