<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "PontoAluno".
 *
 * @property string $id ID
 * @property string $idPonto Ponto
 * @property string $idAluno ALuno
 *
 * @property Aluno $aluno
 * @property Ponto $ponto
 */
class PontoAluno extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PontoAluno';
    }   

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idPonto', 'idAluno'], 'required'],
            [['idPonto', 'idAluno','sentido'], 'integer'],
            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],
            [['idPonto'], 'exist', 'skipOnError' => true, 'targetClass' => Ponto::className(), 'targetAttribute' => ['idPonto' => 'id']],
            [['idAluno', 'sentido'], 'unique', 'targetAttribute' => ['idAluno', 'sentido']]
        ];
    }
    public static function removerTodasRotas($idAluno){
        //self::updateAll(['status' => self::STATUS_ATENDIDO], ['status' => self::STATUS_DEFERIDO, 'idAluno' => $idAluno ] );
        self::deleteAll(['idAluno' => $idAluno]);
        
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idPonto' => 'Ponto',
            'idAluno' => 'ALuno',
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
    public function getPonto()
    {
        return $this->hasOne(Ponto::className(), ['id' => 'idPonto']);
    }

}
