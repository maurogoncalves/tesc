<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "CondutorRotaBackup".
 *
 * @property int $id Código
 * @property int $idCondutor Condutor
 * @property string $descricao Descrição
 * @property int $turno Turno
 * @property int $sentido Sentido
 * @property string $entrada Entrada na escola
 * @property string $saida Saída na escola
 *
 * @property Condutor $condutor
 */
class CondutorRotaBackup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'CondutorRotaBackup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idCondutor', 'turno', 'sentido'], 'integer'],
            [['entrada', 'saida'], 'safe'],
            [['descricao'], 'string', 'max' => 100],
            [['idCondutor'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idCondutor' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idCondutor' => 'Condutor',
            'descricao' => 'Descrição',
            'turno' => 'Turno',
            'sentido' => 'Sentido',
            'entrada' => 'Entrada na escola',
            'saida' => 'Saída na escola',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }
}
